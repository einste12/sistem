<?php

namespace App\Http\Controllers;

use App\Services\ChirpstackService;
use App\Models\LoraDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChirpstackController extends Controller
{
    protected $grpcService;

    public function __construct(ChirpstackService $grpcService)
    {
        $this->grpcService = $grpcService;
    }

    /**
     * Chirpstack'deki cihazları listele ve veritabanına senkronize et
     */
    public function index()
    {
        try {
            // gRPC ile cihazları al
            $response = $this->grpcService->listDevices(50, 0);

            if ($response === null) {
                Log::error('gRPC connection failed');
                return view('back.pages.chirpstack.index')
                    ->with('error', 'ChirpStack gRPC bağlantısı başarısız')
                    ->with('localDevices', LoraDevice::all());
            }

            Log::info('gRPC response received', [
                'total_count' => $response['totalCount'],
                'device_count' => count($response['result'])
            ]);

            // Cihazları senkronize et
            foreach ($response['result'] as $device) {
                $this->syncDevice($device);
            }

            $localDevices = LoraDevice::all();

            return view('back.pages.chirpstack.index', compact('localDevices'))
                ->with('success', count($response['result']) . ' cihaz başarıyla senkronize edildi');

        } catch (\Exception $e) {
            Log::error('Controller error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return view('back.pages.chirpstack.index')
                ->with('error', 'Bir hata oluştu: ' . $e->getMessage())
                ->with('localDevices', LoraDevice::all());
        }
    }

    /**
     * Detaylı cihaz bilgilerini getir
     */
    public function show($id)
    {
        try {
            $localDevice = LoraDevice::findOrFail($id);

            // gRPC ile güncel verileri al
            $response = $this->grpcService->getDevice($localDevice->dev_eui);

            if ($response === null) {
                return redirect()->route('admin.chirpstack.index')
                    ->with('error', 'ChirpStack\'ten cihaz bilgileri alınamadı.');
            }

            // Daha fazla detay almak için
            $activation = $this->grpcService->getDeviceActivation($localDevice->dev_eui);
            $metrics = $this->grpcService->getDeviceMetrics($localDevice->dev_eui);
            $linkMetrics = $this->grpcService->getDeviceLinkMetrics($localDevice->dev_eui);

            $deviceData = $response['device'];
            $deviceStatus = $response['deviceStatus'];

            return view('back.pages.chirpstack.show', compact(
                'localDevice',
                'deviceData',
                'deviceStatus',
                'activation',
                'metrics',
                'linkMetrics'
            ));

        } catch (\Exception $e) {
            Log::error('Show device error: ' . $e->getMessage());

            return redirect()->route('admin.chirpstack.index')
                ->with('error', 'Cihaz bilgileri alınırken hata oluştu.');
        }
    }

    /**
     * ChirpStack'ten gelen cihazı veritabanı ile senkronize et
     */
    protected function syncDevice($grpcDevice)
    {
        try {
            Log::info('Syncing device', ['devEui' => $grpcDevice['devEui']]);

            // DevEUI'ye göre cihazı bul veya oluştur
            $loraDevice = LoraDevice::firstOrNew([
                'dev_eui' => $grpcDevice['devEui']
            ]);

            // Temel bilgileri güncelle
            $loraDevice->name = $grpcDevice['name'] ?? 'ChirpStack Device';
            $loraDevice->description = $grpcDevice['description'] ?? null;
            $loraDevice->application_eui = $grpcDevice['applicationId'] ?? null;
            $loraDevice->is_active = isset($grpcDevice['isDisabled']) ? !$grpcDevice['isDisabled'] : true;

            // Device tipini belirle
            if (isset($grpcDevice['deviceProfileName'])) {
                $loraDevice->device_type = $this->determineDeviceTypeByProfile($grpcDevice['deviceProfileName']);
            } else {
                $loraDevice->device_type = $this->determineDeviceType($grpcDevice);
            }

            // Last seen bilgisini güncelle
            if (isset($grpcDevice['lastSeenAt'])) {
                if ($grpcDevice['lastSeenAt'] instanceof \DateTime) {
                    $loraDevice->last_seen = Carbon::instance($grpcDevice['lastSeenAt']);
                } else {
                    $loraDevice->last_seen = Carbon::parse($grpcDevice['lastSeenAt']);
                }
            }

            // Konfigürasyon bilgilerini güncelle
            $config = $loraDevice->configuration ?? [];
            $config['chirpstack_device_profile_id'] = $grpcDevice['deviceProfileId'] ?? null;
            $config['chirpstack_device_profile_name'] = $grpcDevice['deviceProfileName'] ?? null;
            $config['chirpstack_application_id'] = $grpcDevice['applicationId'] ?? null;
            $config['join_eui'] = $grpcDevice['joinEui'] ?? null;
            $config['skip_fcnt_check'] = $grpcDevice['skipFcntCheck'] ?? false;

            // Variables ve tags varsa ekle
            if (isset($grpcDevice['variables'])) {
                $config['variables'] = $grpcDevice['variables'];
            }

            if (isset($grpcDevice['tags'])) {
                $config['tags'] = $grpcDevice['tags'];
            }

            // Device status varsa ekle
            if (isset($grpcDevice['deviceStatus'])) {
                $config['device_status'] = $grpcDevice['deviceStatus'];

                // Battery level'i ayrı bir kolonda da sakla
                if (isset($grpcDevice['deviceStatus']['batteryLevel'])) {
                    $loraDevice->battery_level = $grpcDevice['deviceStatus']['batteryLevel'];
                }
            }

            $loraDevice->configuration = $config;
            $loraDevice->save();

            Log::info('Device synced successfully', ['devEui' => $grpcDevice['devEui']]);

            return $loraDevice;

        } catch (\Exception $e) {
            Log::error('Error syncing device', [
                'devEui' => $grpcDevice['devEui'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verileri senkronize et (Manual sync)
     */
    public function sync()
    {
        try {
            $offset = 0;
            $limit = 100;
            $totalSynced = 0;
            $hasMore = true;

            while ($hasMore) {
                $response = $this->grpcService->listDevices($limit, $offset);

                if ($response === null || empty($response['result'])) {
                    $hasMore = false;
                    break;
                }

                foreach ($response['result'] as $device) {
                    $this->syncDevice($device);
                    $totalSynced++;
                }

                // Daha fazla cihaz var mı kontrol et
                if (count($response['result']) < $limit) {
                    $hasMore = false;
                } else {
                    $offset += $limit;
                }
            }

            return redirect()->route('admin.chirpstack.index')
                ->with('success', "{$totalSynced} cihaz başarıyla senkronize edildi.");

        } catch (\Exception $e) {
            Log::error('Sync error: ' . $e->getMessage());

            return redirect()->route('admin.chirpstack.index')
                ->with('error', 'Senkronizasyon sırasında bir hata oluştu.');
        }
    }

    /**
     * Downlink mesaj gönder
     */
    public function sendDownlink(Request $request, $id)
    {
        $request->validate([
            'data' => 'required|string',
            'fport' => 'integer|min:1|max:223',
            'confirmed' => 'boolean'
        ]);

        try {
            $device = LoraDevice::findOrFail($id);

            $result = $this->grpcService->enqueueDeviceQueueItem(
                $device->dev_eui,
                $request->data,
                $request->input('fport', 10),
                $request->input('confirmed', false)
            );

            if ($result) {
                return redirect()->route('admin.chirpstack.show', $id)
                    ->with('success', 'Downlink mesajı başarıyla gönderildi. ID: ' . $result['id']);
            } else {
                return redirect()->route('admin.chirpstack.show', $id)
                    ->with('error', 'Downlink mesajı gönderilemedi.');
            }

        } catch (\Exception $e) {
            Log::error('Downlink error: ' . $e->getMessage());

            return redirect()->route('admin.chirpstack.show', $id)
                ->with('error', 'Downlink mesajı gönderilirken bir hata oluştu.');
        }
    }

    /**
     * gRPC bağlantısını test et
     */
    public function testGrpc()
    {
        $results = [];

        try {
            // Test 1: Basic connection
            $results['connection'] = $this->grpcService->testConnection();

            // Test 2: List applications
            $applications = $this->grpcService->listApplications(1, 0);
            $results['applications'] = [
                'success' => $applications !== null,
                'data' => $applications
            ];

            // Test 3: List devices
            $devices = $this->grpcService->listDevices(5, 0);
            $results['devices'] = [
                'success' => $devices !== null,
                'count' => $devices ? count($devices['result']) : 0,
                'data' => $devices
            ];

            // Test 4: Check specific device if any exists
            if ($devices && !empty($devices['result'])) {
                $firstDevice = $devices['result'][0];
                $deviceDetail = $this->grpcService->getDevice($firstDevice['devEui']);

                $results['device_detail'] = [
                    'success' => $deviceDetail !== null,
                    'data' => $deviceDetail
                ];
            }

        } catch (\Exception $e) {
            $results['error'] = [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }

        $results['config'] = [
            'grpc_host' => env('CHIRPSTACK_GRPC_HOST'),
            'api_key_exists' => !empty(env('CHIRPSTACK_API_KEY'))
        ];

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Device profile'a göre cihaz tipini belirle
     */
    protected function determineDeviceTypeByProfile($profileName)
    {
        $profileName = strtolower($profileName);

        if (str_contains($profileName, 'sensor') ||
            str_contains($profileName, 'temperature') ||
            str_contains($profileName, 'humidity')) {
            return 'sensor';
        } elseif (str_contains($profileName, 'actuator') ||
            str_contains($profileName, 'valve') ||
            str_contains($profileName, 'switch')) {
            return 'actuator';
        } elseif (str_contains($profileName, 'gateway')) {
            return 'gateway';
        }

        return 'endpoint';
    }

    /**
     * Cihaz tipini belirle
     */
    protected function determineDeviceType($device)
    {
        if (isset($device['description'])) {
            $description = strtolower($device['description']);
            if (str_contains($description, 'sensor')) {
                return 'sensor';
            } elseif (str_contains($description, 'actuator')) {
                return 'actuator';
            } elseif (str_contains($description, 'gateway')) {
                return 'gateway';
            }
        }

        return 'endpoint';
    }
}
