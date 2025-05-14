<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\LorawanDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LorawanDeviceController extends Controller
{


    public function __construct()
    {

    }

    /**
     * Display a listing of the devices.
     */
    public function index()
    {
        $devices = LorawanDevice::where('user_id', Auth::id())->get();



        // Cihaz istatistikleri
        $stats = [
            'total' => $devices->count(),
            'gateways' => $devices->where('device_type', 'gateway')->count(),
            'sensors' => $devices->where('device_type', 'sensor')->count(),
            'actuators' => $devices->where('device_type', 'actuator')->count(),
            'online' => $devices->where('status', 'online')->count(),
            'offline' => $devices->where('status', 'offline')->count(),
        ];

        return view('back.pages.devices.index', compact('devices', 'stats'));
    }

    /**
     * Show the form for creating a new device.
     */
    public function create()
    {
        // Cihaz tipleri
        $deviceTypes = [
            'gateway' => 'Gateway',
            'sensor' => 'Sensör',
            'actuator' => 'Aktüatör',
            'esp32' => 'ESP32 LoRa',
            'other' => 'Diğer'
        ];

        // Gateway modelleri
        $gatewayModels = [
            'dragino_lps8' => 'Dragino LPS8',
            'milesight_ug65' => 'Milesight UG65',
            'other' => 'Diğer'
        ];

        return view('back.pages.devices.create', compact('deviceTypes', 'gatewayModels'));
    }

    /**
     * Store a newly created device in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'device_type' => 'required|string',
            'model' => 'nullable|string|max:255',
            'device_eui' => 'required|string|max:255|unique:lorawan_devices',
            'application_eui' => 'nullable|string|max:255',
            'application_key' => 'nullable|string|max:255',
            'mac_address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'firmware_version' => 'nullable|string|max:255',
            'hardware_version' => 'nullable|string|max:255',
            'status' => 'nullable|in:online,offline,maintenance',
        ]);

        // Kullanıcı ID'sini ekle
        $validated['user_id'] = Auth::id();

        // Konfigürasyon alanını ekle
        $config = [];
        if ($request->has('config')) {
            $config = $request->config;
        }
        $validated['configuration'] = $config;

        // Cihazı oluştur
        $device = LorawanDevice::create($validated);

        return redirect()->route('lorawan.devices.index')
            ->with('success', 'Cihaz başarıyla eklendi.');
    }

    /**
     * Display the specified device.
     */
    public function show(LorawanDevice $device)
    {
        // Kullanıcının kendi cihazına eriştiğinden emin ol
        if ($device->user_id !== Auth::id()) {
            abort(403, 'Bu cihaza erişim izniniz yok.');
        }

        return view('back.pages.devices.show', compact('device'));
    }

    /**
     * Show the form for editing the specified device.
     */
    public function edit(LorawanDevice $device)
    {
        // Kullanıcının kendi cihazına eriştiğinden emin ol
        if ($device->user_id !== Auth::id()) {
            abort(403, 'Bu cihaza erişim izniniz yok.');
        }

        // Cihaz tipleri
        $deviceTypes = [
            'gateway' => 'Gateway',
            'sensor' => 'Sensör',
            'actuator' => 'Aktüatör',
            'esp32' => 'ESP32 LoRa',
            'other' => 'Diğer'
        ];

        // Gateway modelleri
        $gatewayModels = [
            'dragino_lps8' => 'Dragino LPS8',
            'milesight_ug65' => 'Milesight UG65',
            'other' => 'Diğer'
        ];

        return view('back.pages.devices.edit', compact('device', 'deviceTypes', 'gatewayModels'));
    }

    /**
     * Update the specified device in storage.
     */
    public function update(Request $request, LorawanDevice $device)
    {
        // Kullanıcının kendi cihazına eriştiğinden emin ol
        if ($device->user_id !== Auth::id()) {
            abort(403, 'Bu cihaza erişim izniniz yok.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'device_type' => 'required|string',
            'model' => 'nullable|string|max:255',
            'device_eui' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lorawan_devices')->ignore($device->id),
            ],
            'application_eui' => 'nullable|string|max:255',
            'application_key' => 'nullable|string|max:255',
            'mac_address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'firmware_version' => 'nullable|string|max:255',
            'hardware_version' => 'nullable|string|max:255',
            'status' => 'nullable|in:online,offline,maintenance',
            'battery_level' => 'nullable|integer|min:0|max:100',
        ]);

        // Konfigürasyon alanını güncelle
        if ($request->has('config')) {
            $validated['configuration'] = $request->config;
        }

        // Cihazı güncelle
        $device->update($validated);

        return redirect()->route('lorawan.devices.index')
            ->with('success', 'Cihaz başarıyla güncellendi.');
    }

    /**
     * Remove the specified device from storage.
     */
    public function destroy(LorawanDevice $device)
    {
        // Kullanıcının kendi cihazına eriştiğinden emin ol
        if ($device->user_id !== Auth::id()) {
            abort(403, 'Bu cihaza erişim izniniz yok.');
        }

        $device->delete();

        return redirect()->route('lorawan.devices.index')
            ->with('success', 'Cihaz başarıyla silindi.');
    }

}
