<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LorawanDevice extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'device_type',
        'model',
        'device_eui',
        'application_eui',
        'application_key',
        'mac_address',
        'description',
        'configuration',
        'firmware_version',
        'hardware_version',
        'status',
        'battery_level',
        'last_seen',
        'is_active',
    ];

    // JSON alanını serialize/deserialize etmek için
    protected $casts = [
        'configuration' => 'array',
        'last_seen' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Kullanıcı ilişkisi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Bu LoRaWAN cihazına bağlı tüm LoRa cihazları (Child devices)
    public function loraDevices()
    {
        return $this->hasMany(LoraDevice::class, 'lorawan_device_id');
    }

    // Cihaz tipine göre filtre scope'u
    public function scopeOfType($query, $type)
    {
        return $query->where('device_type', $type);
    }

    // Gateway türündeki cihazlar için scope
    public function scopeGateways($query)
    {
        return $query->where('device_type', 'gateway');
    }

    // Sensor türündeki cihazlar için scope
    public function scopeSensors($query)
    {
        return $query->where('device_type', 'sensor');
    }

    // Çevrimiçi cihazlar için scope
    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    // Çevrimdışı cihazlar için scope
    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    // Alt cihazların durumunu kontrol et
    public function getChildDevicesStatus()
    {
        $childDevices = $this->loraDevices;

        if ($childDevices->isEmpty()) {
            return null;
        }

        $total = $childDevices->count();
        $online = $childDevices->where('status', 'online')->count();
        $offline = $childDevices->where('status', 'offline')->count();
        $maintenance = $childDevices->where('status', 'maintenance')->count();

        return [
            'total' => $total,
            'online' => $online,
            'offline' => $offline,
            'maintenance' => $maintenance,
            'online_percent' => $total > 0 ? round(($online / $total) * 100) : 0
        ];
    }
}
