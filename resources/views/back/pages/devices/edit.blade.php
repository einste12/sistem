@extends('back.layouts.master')

@section('styles')
    <style>
        .form-section {
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f8f9fc;
        }
        .config-fields {
            display: none;
        }
        .form-section-title {
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">LoRaWAN Cihazı Düzenle: {{ $device->name }}</h6>
                        <div>
                            <a href="{{ route('lorawan.devices.show', $device->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Görüntüle
                            </a>
                            <a href="{{ route('lorawan.devices.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('lorawan.devices.update', $device->id) }}" method="POST" id="device-form">
                            @csrf
                            @method('PUT')

                            <!-- Temel Cihaz Bilgileri -->
                            <div class="form-section">
                                <div class="form-section-title">Temel Cihaz Bilgileri</div>

                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Cihaz Adı *</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $device->name) }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="device_type" class="col-sm-2 col-form-label">Cihaz Tipi *</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="device_type" name="device_type" required>
                                            <option value="">Seçiniz</option>
                                            @foreach($deviceTypes as $value => $label)
                                                <option value="{{ $value }}" {{ old('device_type', $device->device_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" id="model-field">
                                    <label for="model" class="col-sm-2 col-form-label">Model</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="model" name="model">
                                            <option value="">Seçiniz</option>
                                            <!-- JS ile doldurulacak -->
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="description" class="col-sm-2 col-form-label">Açıklama</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $device->description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Cihaz Tanımlayıcı Bilgileri -->
                            <div class="form-section">
                                <div class="form-section-title">Cihaz Tanımlayıcı Bilgileri</div>

                                <div class="form-group row">
                                    <label for="device_eui" class="col-sm-2 col-form-label">Device EUI *</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="device_eui" name="device_eui" value="{{ old('device_eui', $device->device_eui) }}" placeholder="örn: 0004A30B001F9ACB" required>
                                        <small class="form-text text-muted">16 karakterli hexadecimal cihaz tanımlayıcısı.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="application_eui" class="col-sm-2 col-form-label">Application EUI</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="application_eui" name="application_eui" value="{{ old('application_eui', $device->application_eui) }}" placeholder="örn: 70B3D57ED0031234">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="application_key" class="col-sm-2 col-form-label">Application Key</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="application_key" name="application_key" value="{{ old('application_key', $device->application_key) }}" placeholder="örn: 2B7E151628AED2A6ABF7158809CF4F3C">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="mac_address" class="col-sm-2 col-form-label">MAC Adresi</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="mac_address" name="mac_address" value="{{ old('mac_address', $device->mac_address) }}" placeholder="örn: 00:1B:44:11:3A:B7">
                                    </div>
                                </div>
                            </div>

                            <!-- Cihaz Durumu ve Versiyonlar -->
                            <div class="form-section">
                                <div class="form-section-title">Cihaz Durumu ve Versiyon Bilgileri</div>

                                <div class="form-group row">
                                    <label for="status" class="col-sm-2 col-form-label">Durum</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="status" name="status">
                                            <option value="offline" {{ old('status', $device->status) == 'offline' ? 'selected' : '' }}>Çevrimdışı</option>
                                            <option value="online" {{ old('status', $device->status) == 'online' ? 'selected' : '' }}>Çevrimiçi</option>
                                            <option value="maintenance" {{ old('status', $device->status) == 'maintenance' ? 'selected' : '' }}>Bakımda</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="battery_level" class="col-sm-2 col-form-label">Pil Seviyesi (%)</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="battery_level" name="battery_level" value="{{ old('battery_level', $device->battery_level) }}" min="0" max="100">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="firmware_version" class="col-sm-2 col-form-label">Firmware Versiyonu</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="firmware_version" name="firmware_version" value="{{ old('firmware_version', $device->firmware_version) }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="hardware_version" class="col-sm-2 col-form-label">Hardware Versiyonu</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="hardware_version" name="hardware_version" value="{{ old('hardware_version', $device->hardware_version) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Gateway özellikleri (sadece gateway seçildiğinde görünür) -->
                            <div class="form-section config-fields" id="gateway-config">
                                <div class="form-section-title">Gateway Konfigürasyonu</div>

                                <div class="form-group row">
                                    <label for="config[frequency_plan]" class="col-sm-2 col-form-label">Frekans Planı</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="frequency_plan" name="config[frequency_plan]">
                                            <option value="EU868" {{ (isset($device->configuration['frequency_plan']) && $device->configuration['frequency_plan'] == 'EU868') ? 'selected' : '' }}>EU868</option>
                                            <option value="US915" {{ (isset($device->configuration['frequency_plan']) && $device->configuration['frequency_plan'] == 'US915') ? 'selected' : '' }}>US915</option>
                                            <option value="AS923" {{ (isset($device->configuration['frequency_plan']) && $device->configuration['frequency_plan'] == 'AS923') ? 'selected' : '' }}>AS923</option>
                                            <option value="AU915" {{ (isset($device->configuration['frequency_plan']) && $device->configuration['frequency_plan'] == 'AU915') ? 'selected' : '' }}>AU915</option>
                                            <option value="KR920" {{ (isset($device->configuration['frequency_plan']) && $device->configuration['frequency_plan'] == 'KR920') ? 'selected' : '' }}>KR920</option>
                                            <option value="IN865" {{ (isset($device->configuration['frequency_plan']) && $device->configuration['frequency_plan'] == 'IN865') ? 'selected' : '' }}>IN865</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="config[ip_address]" class="col-sm-2 col-form-label">IP Adresi</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="ip_address" name="config[ip_address]" value="{{ isset($device->configuration['ip_address']) ? $device->configuration['ip_address'] : '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="config[location_lat]" class="col-sm-2 col-form-label">Konum (Enlem)</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="location_lat" name="config[location_lat]" value="{{ isset($device->configuration['location_lat']) ? $device->configuration['location_lat'] : '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="config[location_lon]" class="col-sm-2 col-form-label">Konum (Boylam)</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="location_lon" name="config[location_lon]" value="{{ isset($device->configuration['location_lon']) ? $device->configuration['location_lon'] : '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="config[antenna_gain]" class="col-sm-2 col-form-label">Anten Kazancı (dBi)</label>
                                    <div class="col-sm-10">
                                        <input type="number" step="0.1" class="form-control" id="antenna_gain" name="config[antenna_gain]" value="{{ isset($device->configuration['antenna_gain']) ? $device->configuration['antenna_gain'] : '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Sensör özellikleri (sadece sensör seçildiğinde görünür) -->
                            <div class="form-section config-fields" id="sensor-config">
                                <div class="form-section-title">Sensör Konfigürasyonu</div>

                                <div class="form-group row">
                                    <label for="config[sensor_type]" class="col-sm-2 col-form-label">Sensör Tipi</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="sensor_type" name="config[sensor_type]">
                                            <option value="temperature" {{ (isset($device->configuration['sensor_type']) && $device->configuration['sensor_type'] == 'temperature') ? 'selected' : '' }}>Sıcaklık</option>
                                            <option value="humidity" {{ (isset($device->configuration['sensor_type']) && $device->configuration['sensor_type'] == 'humidity') ? 'selected' : '' }}>Nem</option>
                                            <option value="pressure" {{ (isset($device->configuration['sensor_type']) && $device->configuration['sensor_type'] == 'pressure') ? 'selected' : '' }}>Basınç</option>
                                            <option value="co2" {{ (isset($device->configuration['sensor_type']) && $device->configuration['sensor_type'] == 'co2') ? 'selected' : '' }}>CO2</option>
                                            <option value="light" {{ (isset($device->configuration['sensor_type']) && $device->configuration['sensor_type'] == 'light') ? 'selected' : '' }}>Işık</option>
                                            <option value="motion" {{ (isset($device->configuration['sensor_type']) && $device->configuration['sensor_type'] == 'motion') ? 'selected' : '' }}>Hareket</option>
                                            <option value="soil_moisture" {{ (isset($device->configuration['sensor_type']) && $device->configuration['sensor_type'] == 'soil_moisture') ? 'selected' : '' }}>Toprak Nemi</option>
                                            <option value="other" {{ (isset($device->configuration['sensor_type']) && $device->configuration['sensor_type'] == 'other') ? 'selected' : '' }}>Diğer</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="config[measurement_unit]" class="col-sm-2 col-form-label">Ölçüm Birimi</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="measurement_unit" name="config[measurement_unit]" value="{{ isset($device->configuration['measurement_unit']) ? $device->configuration['measurement_unit'] : '' }}">
                                        <small class="form-text text-muted">Örn: °C, %, hPa, lux, ppm</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="config[min_value]" class="col-sm-2 col-form-label">Minimum Değer</label>
                                    <div class="col-sm-10">
                                        <input type="number" step="0.01" class="form-control" id="min_value" name="config[min_value]" value="{{ isset($device->configuration['min_value']) ? $device->configuration['min_value'] : '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="config[max_value]" class="col-sm-2 col-form-label">Maksimum Değer</label>
                                    <div class="col-sm-10">
                                        <input type="number" step="0.01" class="form-control" id="max_value" name="config[max_value]" value="{{ isset($device->configuration['max_value']) ? $device->configuration['max_value'] : '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="config[report_interval]" class="col-sm-2 col-form-label">Raporlama Aralığı (dk)</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="report_interval" name="config[report_interval]" value="{{ isset($device->configuration['report_interval']) ? $device->configuration['report_interval'] : '15' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Cihaz tipine göre model seçeneklerini doldur
            var gatewayModels = {
                'dragino_lps8': 'Dragino LPS8',
                'milesight_ug65': 'Milesight UG65',
                'other': 'Diğer'
            };

            var sensorModels = {
                'esp32_lora_ra02': 'ESP32 LoRa RA02',
                'dragino_lht65': 'Dragino LHT65',
                'milesight_em300': 'Milesight EM300',
                'milesight_vs121': 'Milesight VS121',
                'other': 'Diğer'
            };

            var actuatorModels = {
                'esp32_lora_ra02': 'ESP32 LoRa RA02',
                'dragino_lsn50': 'Dragino LSN50',
                'other': 'Diğer'
            };

            // Sayfa yüklendiğinde mevcut model değerini kaydet
            var currentModel = "{{ $device->model }}";

            // Cihaz tipi değiştiğinde
            $('#device_type').change(function() {
                updateModelOptions();
            });

            function updateModelOptions() {
                var deviceType = $('#device_type').val();
                var $modelSelect = $('#model');

                // Model seçim alanını temizle
                $modelSelect.empty().append('<option value="">Seçiniz</option>');

                // Cihaz tipine göre model seçeneklerini ekle
                if (deviceType === 'gateway') {
                    $.each(gatewayModels, function(value, label) {
                        $modelSelect.append($('<option>', {
                            value: value,
                            text: label,
                            selected: (value === currentModel)
                        }));
                    });
                    // Gateway konfigürasyon alanını göster
                    $('.config-fields').hide();
                    $('#gateway-config').show();
                } else if (deviceType === 'sensor') {
                    $.each(sensorModels, function(value, label) {
                        $modelSelect.append($('<option>', {
                            value: value,
                            text: label,
                            selected: (value === currentModel)
                        }));
                    });
                    // Sensör konfigürasyon alanını göster
                    $('.config-fields').hide();
                    $('#sensor-config').show();
                } else if (deviceType === 'actuator') {
                    $.each(actuatorModels, function(value, label) {
                        $modelSelect.append($('<option>', {
                            value: value,
                            text: label,
                            selected: (value === currentModel)
                        }));
                    });
                    // Aktüatör konfigürasyon alanını göster, şimdilik sensör ayarlarını kullanıyoruz
                    $('.config-fields').hide();
                    $('#sensor-config').show();
                } else if (deviceType === 'esp32') {
                    $modelSelect.append($('<option>', {
                        value: 'esp32_lora_ra02',
                        text: 'ESP32 LoRa RA02',
                        selected: (currentModel === 'esp32_lora_ra02')
                    }));
                    // ESP32 için özel alanları göster
                    $('.config-fields').hide();
                } else {
                    // Diğer cihaz tipleri için konfigürasyon alanlarını gizle
                    $('.config-fields').hide();
                }
            }

            // Sayfa yüklendiğinde seçili cihaz tipine göre model seçeneklerini doldur
            updateModelOptions();

            // Device EUI formatını düzenle
            $('#device_eui').on('input', function() {
                var eui = $(this).val().replace(/[^0-9A-Fa-f]/g, '').toUpperCase();
                $(this).val(eui);
            });

            // MAC adresi formatını düzenle (XX:XX:XX:XX:XX:XX)
            $('#mac_address').on('input', function() {
                var mac = $(this).val().replace(/[^0-9A-Fa-f:]/g, '').toUpperCase();

                // Eğer kullanıcı ":" karakteri girmemişse, her 2 karakterden sonra ":" ekle
                if (!mac.includes(':')) {
                    mac = mac.replace(/([0-9A-F]{2})(?=.)/g, '$1:');
                }

                $(this).val(mac);
            });
        });
    </script>
@endsection
