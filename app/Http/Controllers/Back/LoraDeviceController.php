<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\LoraDevice;
use App\Models\LorawanDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LoraDeviceController extends Controller
{
    /**
     * Constructor for the controller.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the devices.
     */
    public function index(Request $request)
    {
        // Filtreler
        $status = $request->get('status');
        $deviceType = $request->get('device_type');
        $parentId = $request->get('parent_id');

        // Sorguyu oluştur
        $query = LoraDevice::with('lorawanDevice');

        // Kullanıcıya ait cihazları getir
        $query->where('user_id', Auth::id());

        // Filtreler
        if ($status) {
            $query->where('status', $status);
        }

        if ($deviceType) {
            $query->where('device_type', $deviceType);
        }

        if ($parentId) {
            $query->where('lorawan_device_id', $parentId);
        }

        // Cihazları getir
        $devices = $query->get();

        // LoRaWAN cihazları (parent seçeneği için)
        $parentDevices = LorawanDevice::all();

        // Cihaz tipleri
        $deviceTypes = [
            'sensor' => 'Sensör',
            'actuator' => 'Aktüatör',
            'endpoint' => 'Uç Nokta',
            'other' => 'Diğer'
        ];

        // Cihaz istatistikleri
        $stats = [
            'total' => $devices->count(),
            'sensors' => $devices->where('device_type', 'sensor')->count(),
            'actuators' => $devices->where('device_type', 'actuator')->count(),
            'endpoints' => $devices->where('device_type', 'endpoint')->count(),
            'online' => $devices->where('status', 'online')->count(),
            'offline' => $devices->where('status', 'offline')->count(),
            'maintenance' => $devices->where('status', 'maintenance')->count(),
        ];

        return view('back.pages.lora.index', compact('devices', 'stats', 'parentDevices', 'deviceTypes'));
    }

    /**
     * Show the form for creating a new device.
     */
    public function create(Request $request)
    {

        // Kullanıcının tüm LoRaWAN cihazlarını getir (parent için seçenek)
        $parentDevices = LorawanDevice::all();

        // Cihaz tipleri
        $deviceTypes = [
            'sensor' => 'Sensör',
            'actuator' => 'Aktüatör',
            'endpoint' => 'Uç Nokta',
            'other' => 'Diğer'
        ];

        // Cihaz modelleri
        $sensorModels = [
            'temperature' => 'Sıcaklık Sensörü',
            'humidity' => 'Nem Sensörü',
            'temp_humidity' => 'Sıcaklık & Nem Sensörü',
            'pressure' => 'Basınç Sensörü',
            'light' => 'Işık Sensörü',
            'motion' => 'Hareket Sensörü',
            'door' => 'Kapı Sensörü',
            'water' => 'Su Sensörü',
            'soil' => 'Toprak Nem Sensörü',
            'gas' => 'Gaz Sensörü',
            'other' => 'Diğer'
        ];

        $actuatorModels = [
            'relay' => 'Röle',
            'switch' => 'Anahtar',
            'valve' => 'Vana',
            'motor' => 'Motor Kontrolü',
            'led' => 'LED Kontrolü',
            'buzzer' => 'Buzzer',
            'other' => 'Diğer'
        ];

        return view('back.pages.lora.create', compact('parentDevices', 'deviceTypes', 'sensorModels', 'actuatorModels'));
    }

    /**
     * Store a newly created device in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lorawan_device_id' => 'required|exists:lorawan_devices,id',
            'name' => 'required|string|max:255',
            'device_type' => 'required|string',
            'model' => 'nullable|string|max:255',
            'dev_eui' => 'required|string|max:255|unique:lora_devices',
            'application_eui' => 'nullable|string|max:255',
            'application_key' => 'nullable|string|max:255',
            'f_port' => 'nullable|integer|min:1|max:223',
            'mac_address' => 'nullable|string|max:255',
            'firmware_version' => 'nullable|string|max:255',
            'hardware_version' => 'nullable|string|max:255',
            'status' => 'nullable|in:online,offline,maintenance',
            'battery_level' => 'nullable|integer|min:0|max:100',
        ]);

        // Parent LoRaWAN cihazını kontrol et
        $parentDevice = LorawanDevice::findOrFail($request->lorawan_device_id);


        // Kullanıcı ID'sini ekle
        $validated['user_id'] = Auth::id();

        // Konfigürasyon alanını ekle
        $config = [];
        if ($request->has('config')) {
            $config = $request->config;
        }
        $validated['configuration'] = $config;

        // Sensör ve aktüatör alanlarını yönet
        if ($request->device_type === 'sensor') {
            // Sensör için varsayılan değerler
            $validated['temperature'] = $request->temperature ?? null;
            $validated['humidity'] = $request->humidity ?? null;
        } elseif ($request->device_type === 'actuator') {
            // Aktüatör için varsayılan değerler
            $validated['digital_output_1'] = $request->filled('digital_output_1') ? (bool)$request->digital_output_1 : null;
            $validated['digital_output_2'] = $request->filled('digital_output_2') ? (bool)$request->digital_output_2 : null;
        }

        // Cihazı oluştur
        $device = LoraDevice::create($validated);

        return redirect()->route('admin.lora.devices.show', $device->id)
            ->with('success', 'LoRa cihazı başarıyla eklendi.');
    }

    /**
     * Display the specified device.
     */
    public function show(LoraDevice $device)
    {
        // Kullanıcının kendi cihazına eriştiğinden emin ol
        if ($device->user_id !== Auth::id()) {
            abort(403, 'Bu cihaza erişim izniniz yok.');
        }

        // Parent cihaz bilgisini getir
        $device->load('lorawanDevice');

        return view('back.pages.lora.show', compact('device'));
    }

    /**
     * Show the form for editing the specified device.
     */
    public function edit(LoraDevice $device)
    {


        // Parent cihaz bilgisini getir
        $device->load('lorawanDevice');

        // Kullanıcının tüm LoRaWAN cihazlarını getir (parent için seçenek)
        $parentDevices = LorawanDevice::all();

        // Cihaz tipleri
        $deviceTypes = [
            'sensor' => 'Sensör',
            'actuator' => 'Aktüatör',
            'endpoint' => 'Uç Nokta',
            'other' => 'Diğer'
        ];

        // Cihaz modelleri
        $sensorModels = [
            'temperature' => 'Sıcaklık Sensörü',
            'humidity' => 'Nem Sensörü',
            'temp_humidity' => 'Sıcaklık & Nem Sensörü',
            'pressure' => 'Basınç Sensörü',
            'light' => 'Işık Sensörü',
            'motion' => 'Hareket Sensörü',
            'door' => 'Kapı Sensörü',
            'water' => 'Su Sensörü',
            'soil' => 'Toprak Nem Sensörü',
            'gas' => 'Gaz Sensörü',
            'other' => 'Diğer'
        ];

        $actuatorModels = [
            'relay' => 'Röle',
            'switch' => 'Anahtar',
            'valve' => 'Vana',
            'motor' => 'Motor Kontrolü',
            'led' => 'LED Kontrolü',
            'buzzer' => 'Buzzer',
            'other' => 'Diğer'
        ];

        return view('back.pages.lora.edit', compact('device', 'parentDevices', 'deviceTypes', 'sensorModels', 'actuatorModels'));
    }

    /**
     * Update the specified device in storage.
     */
    public function update(Request $request, LoraDevice $device)
    {
        // Kullanıcının kendi cihazına eriştiğinden emin ol
        if ($device->user_id !== Auth::id()) {
            abort(403, 'Bu cihaza erişim izniniz yok.');
        }

        $validated = $request->validate([
            'lorawan_device_id' => 'required|exists:lorawan_devices,id',
            'name' => 'required|string|max:255',
            'device_type' => 'required|string',
            'model' => 'nullable|string|max:255',
            'dev_eui' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lora_devices')->ignore($device->id),
            ],
            'application_eui' => 'nullable|string|max:255',
            'application_key' => 'nullable|string|max:255',
            'f_port' => 'nullable|integer|min:1|max:223',
            'mac_address' => 'nullable|string|max:255',
            'firmware_version' => 'nullable|string|max:255',
            'hardware_version' => 'nullable|string|max:255',
            'status' => 'nullable|in:online,offline,maintenance',
            'battery_level' => 'nullable|integer|min:0|max:100',
        ]);

        // Parent LoRaWAN cihazını kontrol et
        $parentDevice = LorawanDevice::findOrFail($request->lorawan_device_id);


        // Konfigürasyon alanını güncelle
        if ($request->has('config')) {
            $validated['configuration'] = $request->config;
        }

        // Sensör ve aktüatör alanlarını yönet
        if ($request->device_type === 'sensor') {
            // Sensör için değerleri güncelle
            $validated['temperature'] = $request->temperature ?? null;
            $validated['humidity'] = $request->humidity ?? null;
            // Aktüatör alanlarını temizle
            $validated['digital_output_1'] = null;
            $validated['digital_output_2'] = null;
        } elseif ($request->device_type === 'actuator') {
            // Aktüatör için değerleri güncelle
            $validated['digital_output_1'] = $request->filled('digital_output_1') ? (bool)$request->digital_output_1 : null;
            $validated['digital_output_2'] = $request->filled('digital_output_2') ? (bool)$request->digital_output_2 : null;
            // Sensör alanlarını temizle
            $validated['temperature'] = null;
            $validated['humidity'] = null;
        } else {
            // Diğer cihaz tipleri için alan temizleme
            $validated['temperature'] = null;
            $validated['humidity'] = null;
            $validated['digital_output_1'] = null;
            $validated['digital_output_2'] = null;
        }

        // Cihazı güncelle
        $device->update($validated);

        return redirect()->route('admin.lora.devices.show', $device->id)
            ->with('success', 'LoRa cihazı başarıyla güncellendi.');
    }

    /**
     * Remove the specified device from storage.
     */
    public function destroy(LoraDevice $device)
    {


        $device->delete();

        return redirect()->route('admin.lora.devices.index')
            ->with('success', 'LoRa cihazı başarıyla silindi.');
    }

    /**
     * Toggle device digital output
     */
    public function toggleOutput(Request $request, LoraDevice $device)
    {
        // Kullanıcının kendi cihazına eriştiğinden emin ol
        if ($device->user_id !== Auth::id()) {
            abort(403, 'Bu cihaza erişim izniniz yok.');
        }

        $request->validate([
            'output' => 'required|in:1,2',
            'value' => 'required|boolean',
        ]);

        // Aktüatör kontrolü
        if ($device->device_type !== 'actuator') {
            return response()->json([
                'success' => false,
                'message' => 'Bu cihaz bir aktüatör değil.'
            ], 400);
        }

        $output = $request->output;
        $value = (bool) $request->value;

        $fieldName = "digital_output_{$output}";
        $device->$fieldName = $value;
        $device->save();

        // Burada gerçek hayatta, LoRaWAN ağı üzerinden cihaza komut gönderme işlemi yapılır
        // Bu örneğimizde sadece veritabanındaki değeri güncelliyoruz

        return response()->json([
            'success' => true,
            'message' => "Dijital çıkış {$output} " . ($value ? 'açıldı' : 'kapatıldı'),
            'value' => $value
        ]);
    }
}
