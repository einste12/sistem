<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoraDevice extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        // Parent LoRaWAN cihaz referansı
        'lorawan_device_id',
        // Opsiyonel kullanıcı ilişkisi
        'user_id',
        // Genel cihaz meta bilgisi
        'name',
        'device_type',
        'model',
        // LoRaWAN protokol kimlik bilgileri
        'dev_eui',
        'application_eui',
        'application_key',
        'f_port',
        // Ağ ve konfigürasyon
        'mac_address',
        'configuration',
        // Firmware / hardware sürümleri
        'firmware_version',
        'hardware_version',
        // Durum ve telemetri
        'status',
        'battery_level',
        'last_seen',
        'is_active',
        // ===== Sensör & IO Alanları =====
        // Isı / nem
        'temperature',
        'humidity',
        // Dijital girişler
        'digital_input_1',
        'digital_input_2',
        'digital_input_3',
        // Dijital çıkışlar
        'digital_output_1',
        'digital_output_2',
    ];

    // JSON alanını serialize/deserialize etmek için
    protected $casts = [
        'configuration' => 'array',
        'last_seen' => 'datetime',
        'is_active' => 'boolean',
        'digital_input_1' => 'boolean',
        'digital_input_2' => 'boolean',
        'digital_input_3' => 'boolean',
        'digital_output_1' => 'boolean',
        'digital_output_2' => 'boolean',
        'battery_level' => 'integer',
        'temperature' => 'float',
        'humidity' => 'float',
    ];

    /**
     * Bu LoRa cihazının bağlı olduğu ana LoRaWAN cihazı
     */
    public function lorawanDevice()
    {
        return $this->belongsTo(LorawanDevice::class, 'lorawan_device_id');
    }

    /**
     * Bu cihazın sahibi olan kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Çevrimiçi cihazlar için scope
     */
    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    /**
     * Çevrimdışı cihazlar için scope
     */
    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    /**
     * Sensör türündeki cihazlar için scope
     */
    public function scopeSensors($query)
    {
        return $query->where('device_type', 'sensor');
    }

    /**
     * Aktüatör türündeki cihazlar için scope
     */
    public function scopeActuators($query)
    {
        return $query->where('device_type', 'actuator');
    }

    /**
     * Aktif cihazlar için scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Pil seviyesi düşük cihazlar için scope
     */
    public function scopeLowBattery($query, $threshold = 20)
    {
        return $query->whereNotNull('battery_level')
            ->where('battery_level', '<=', $threshold);
    }

    /**
     * Cihazın sensör verilerini alır
     */
    public function getSensorData()
    {
        $data = [];

        if ($this->temperature !== null) {
            $data['temperature'] = $this->temperature;
        }

        if ($this->humidity !== null) {
            $data['humidity'] = $this->humidity;
        }

        // Dijital girişler
        for ($i = 1; $i <= 3; $i++) {
            $field = "digital_input_{$i}";
            if ($this->$field !== null) {
                $data[$field] = $this->$field;
            }
        }

        return $data;
    }

    /**
     * Cihazın dijital çıkışlarını alır
     */
    public function getDigitalOutputs()
    {
        $outputs = [];

        for ($i = 1; $i <= 2; $i++) {
            $field = "digital_output_{$i}";
            $outputs[$field] = $this->$field;
        }

        return $outputs;
    }

    /**
     * Cihazın dijital çıkışını ayarla
     */
    public function setDigitalOutput($output, $value)
    {
        $field = "digital_output_{$output}";

        if (in_array($field, ['digital_output_1', 'digital_output_2'])) {
            $this->$field = (bool) $value;
            return $this->save();
        }

        return false;
    }

    /**
     * Configuration değerini JSON formatında kaydet
     */
    protected function setConfigurationAttribute($value)
    {
        $this->attributes['configuration'] = is_array($value) ? json_encode($value) : $value;
    }
}
