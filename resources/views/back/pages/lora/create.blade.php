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
    @section('js')
        <script>
            $(document).ready(function() {
                // Cihaz tipine göre model seçeneklerini doldur
                var sensorModels = {
                    'temperature': 'Sıcaklık Sensörü',
                    'humidity': 'Nem Sensörü',
                    'temp_humidity': 'Sıcaklık & Nem Sensörü',
                    'pressure': 'Basınç Sensörü',
                    'light': 'Işık Sensörü',
                    'motion': 'Hareket Sensörü',
                    'door': 'Kapı Sensörü',
                    'water': 'Su Sensörü',
                    'soil': 'Toprak Nem Sensörü',
                    'gas': 'Gaz Sensörü',
                    'other': 'Diğer'
                };

                var actuatorModels = {
                    'relay': 'Röle',
                    'switch': 'Anahtar',
                    'valve': 'Vana',
                    'motor': 'Motor Kontrolü',
                    'led': 'LED Kontrolü',
                    'buzzer': 'Buzzer',
                    'other': 'Diğer'
                };

                // Cihaz tipi değiştiğinde
                $('#device_type').change(function() {
                    var deviceType = $(this).val();
                    var $modelSelect = $('#model');

                    // Model seçim alanını temizle
                    $modelSelect.empty().append('<option value="">Seçiniz</option>');

                    // Cihaz tipine göre model seçeneklerini ekle
                    if (deviceType === 'sensor') {
                        $.each(sensorModels, function(value, label) {
                            $modelSelect.append($('<option>', {
                                value: value,
                                text: label
                            }));
                        });
                        // Sensör konfigürasyon alanını göster
                        $('.config-fields').hide();
                        $('#sensor-config').show();
                    } else if (deviceType === 'actuator') {
                        $.each(actuatorModels, function(value, label) {
                            $modelSelect.append($('<option>', {
                                value: value,
                                text: label
                            }));
                        });
                        // Aktüatör konfigürasyon alanını göster
                        $('.config-fields').hide();
                        $('#actuator-config').show();
                    } else {
                        // Diğer cihaz tipleri için konfigürasyon alanlarını gizle
                        $('.config-fields').hide();
                    }
                });

                // Sayfa yüklendiğinde seçili cihaz tipine göre model seçeneklerini doldur
                $('#device_type').trigger('change');

                // DEV EUI formatını düzenle
                $('#dev_eui').on('input', function() {
                    var eui = $(this).val().replace(/[^0-9A-Fa-f]/g, '').toUpperCase();
                    $(this).val(eui);
                });

                // MAC adresi formatını düzenle (XX:XX:XX:XX:XX:XX)
                $('#mac_address').on('input', function() {
                    var mac = $(this).val().replace(/[^0-9A-Fa-f:]/g, '').toUpperCase();

                    // Eğer kullanıcı ":" karakteri girmemişse, her 2 karakterden sonra ":" ekle
                    if (!mac.includes(':') && mac.length > 2) {
                        mac = mac.replace(/([0-9A-F]{2})(?=.)/g, '$1:');
                    }

                    $(this).val(mac);
                });

                // Application Key göster/gizle
                $('#toggleAppKey').click(function() {
                    var $input = $('#application_key');
                    var $icon = $(this).find('i');

                    if ($input.attr('type') === 'password') {
                        $input.attr('type', 'text');
                        $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        $input.attr('type', 'password');
                        $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                });
            });
        </script>
    @endsection

    @section('content')
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Yeni LoRa Cihazı Ekle</h6>
                            <div>
                                <a href="{{ route('admin.lora.devices.index') }}" class="btn btn-secondary btn-sm">
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

                            <form action="{{ route('admin.lora.devices.store') }}" method="POST" id="device-form">
                                @csrf

                                <!-- Parent Cihaz Seçimi -->
                                <div class="form-section">
                                    <div class="form-section-title">Parent LoRaWAN Cihazı</div>

                                    <div class="form-group row">
                                        <label for="lorawan_device_id" class="col-sm-2 col-form-label">LoRaWAN Cihazı *</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" id="lorawan_device_id" name="lorawan_device_id" required>
                                                <option value="">Seçiniz</option>
                                                @foreach($parentDevices as $parent)
                                                    <option value="{{ $parent->id }}">
                                                        {{ $parent->name }} ({{ $parent->device_type }}) - EUI: {{ $parent->device_eui }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Bu LoRa cihazının bağlı olacağı LoRaWAN cihazını seçin.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Temel Cihaz Bilgileri -->
                                <div class="form-section">
                                    <div class="form-section-title">Temel Cihaz Bilgileri</div>

                                    <div class="form-group row">
                                        <label for="name" class="col-sm-2 col-form-label">Cihaz Adı *</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="device_type" class="col-sm-2 col-form-label">Cihaz Tipi *</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" id="device_type" name="device_type" required>
                                                <option value="">Seçiniz</option>
                                                @foreach($deviceTypes as $value => $label)
                                                    <option value="{{ $value }}" {{ old('device_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
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
                                </div>

                                <!-- Cihaz Tanımlayıcı Bilgileri -->
                                <div class="form-section">
                                    <div class="form-section-title">Cihaz Tanımlayıcı Bilgileri</div>

                                    <div class="form-group row">
                                        <label for="dev_eui" class="col-sm-2 col-form-label">DEV EUI *</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="dev_eui" name="dev_eui" value="{{ old('dev_eui') }}" placeholder="örn: 0004A30B001F9ACB" required>
                                            <small class="form-text text-muted">16 karakterli hexadecimal cihaz tanımlayıcısı.</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="application_eui" class="col-sm-2 col-form-label">Application EUI</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="application_eui" name="application_eui" value="{{ old('application_eui') }}" placeholder="örn: 70B3D57ED0031234">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="application_key" class="col-sm-2 col-form-label">Application Key</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="application_key" name="application_key" value="{{ old('application_key') }}" placeholder="örn: 2B7E151628AED2A6ABF7158809CF4F3C">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" id="toggleAppKey">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="f_port" class="col-sm-2 col-form-label">F-Port</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" id="f_port" name="f_port" value="{{ old('f_port') }}" min="1" max="223" placeholder="örn: 1">
                                            <small class="form-text text-muted">LoRaWAN cihaz trafik portu (1-223 arası).</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="mac_address" class="col-sm-2 col-form-label">MAC Adresi</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="mac_address" name="mac_address" value="{{ old('mac_address') }}" placeholder="örn: 00:1B:44:11:3A:B7">
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
                                                <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>Çevrimdışı</option>
                                                <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>Çevrimiçi</option>
                                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Bakımda</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="battery_level" class="col-sm-2 col-form-label">Pil Seviyesi (%)</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" id="battery_level" name="battery_level" value="{{ old('battery_level') }}" min="0" max="100">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="firmware_version" class="col-sm-2 col-form-label">Firmware Versiyonu</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="firmware_version" name="firmware_version" value="{{ old('firmware_version') }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="hardware_version" class="col-sm-2 col-form-label">Hardware Versiyonu</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="hardware_version" name="hardware_version" value="{{ old('hardware_version') }}">
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
                                                <option value="temperature" {{ old('config.sensor_type') == 'temperature' ? 'selected' : '' }}>Sıcaklık</option>
                                                <option value="humidity" {{ old('config.sensor_type') == 'humidity' ? 'selected' : '' }}>Nem</option>
                                                <option value="temp_humidity" {{ old('config.sensor_type') == 'temp_humidity' ? 'selected' : '' }}>Sıcaklık & Nem</option>
                                                <option value="pressure" {{ old('config.sensor_type') == 'pressure' ? 'selected' : '' }}>Basınç</option>
                                                <option value="light" {{ old('config.sensor_type') == 'light' ? 'selected' : '' }}>Işık</option>
                                                <option value="motion" {{ old('config.sensor_type') == 'motion' ? 'selected' : '' }}>Hareket</option>
                                                <option value="door" {{ old('config.sensor_type') == 'door' ? 'selected' : '' }}>Kapı</option>
                                                <option value="water" {{ old('config.sensor_type') == 'water' ? 'selected' : '' }}>Su</option>
                                                <option value="soil" {{ old('config.soil_moisture') == 'soil' ? 'selected' : '' }}>Toprak Nemi</option>
                                                <option value="other" {{ old('config.sensor_type') == 'other' ? 'selected' : '' }}>Diğer</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="config[measurement_unit]" class="col-sm-2 col-form-label">Ölçüm Birimi</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="measurement_unit" name="config[measurement_unit]" value="{{ old('config.measurement_unit') }}">
                                            <small class="form-text text-muted">Örn: °C, %, hPa, lux, ppm</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="config[min_value]" class="col-sm-2 col-form-label">Minimum Değer</label>
                                        <div class="col-sm-10">
                                            <input type="number" step="0.01" class="form-control" id="min_value" name="config[min_value]" value="{{ old('config.min_value') }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="config[max_value]" class="col-sm-2 col-form-label">Maksimum Değer</label>
                                        <div class="col-sm-10">
                                            <input type="number" step="0.01" class="form-control" id="max_value" name="config[max_value]" value="{{ old('config.max_value') }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="config[report_interval]" class="col-sm-2 col-form-label">Raporlama Aralığı (dk)</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" id="report_interval" name="config[report_interval]" value="{{ old('config.report_interval', 15) }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="temperature" class="col-sm-2 col-form-label">Sıcaklık</label>
                                        <div class="col-sm-10">
                                            <input type="number" step="0.1" class="form-control" id="temperature" name="temperature" value="{{ old('temperature') }}">
                                            <small class="form-text text-muted">Başlangıç değeri (°C)</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="humidity" class="col-sm-2 col-form-label">Nem</label>
                                        <div class="col-sm-10">
                                            <input type="number" step="0.1" class="form-control" id="humidity" name="humidity" value="{{ old('humidity') }}">
                                            <small class="form-text text-muted">Başlangıç değeri (%)</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Aktüatör özellikleri (sadece aktüatör seçildiğinde görünür) -->
                                <div class="form-section config-fields" id="actuator-config">
                                    <div class="form-section-title">Aktüatör Konfigürasyonu</div>

                                    <div class="form-group row">
                                        <label for="config[actuator_type]" class="col-sm-2 col-form-label">Aktüatör Tipi</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" id="actuator_type" name="config[actuator_type]">
                                                <option value="relay" {{ old('config.actuator_type') == 'relay' ? 'selected' : '' }}>Röle</option>
                                                <option value="switch" {{ old('config.actuator_type') == 'switch' ? 'selected' : '' }}>Anahtar</option>
                                                <option value="valve" {{ old('config.actuator_type') == 'valve' ? 'selected' : '' }}>Vana</option>
                                                <option value="motor" {{ old('config.actuator_type') == 'motor' ? 'selected' : '' }}>Motor</option>
                                                <option value="led" {{ old('config.actuator_type') == 'led' ? 'selected' : '' }}>LED</option>
                                                <option value="buzzer" {{ old('config.actuator_type') == 'buzzer' ? 'selected' : '' }}>Buzzer</option>
                                                <option value="other" {{ old('config.actuator_type') == 'other' ? 'selected' : '' }}>Diğer</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="config[channel_count]" class="col-sm-2 col-form-label">Kanal Sayısı</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" id="channel_count" name="config[channel_count]" value="{{ old('config.channel_count', 1) }}" min="1" max="8">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="digital_output_1" class="col-sm-2 col-form-label">Dijital Çıkış 1</label>
                                        <div class="col-sm-10">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="digital_output_1" name="digital_output_1" value="1" {{ old('digital_output_1') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="digital_output_1">Açık</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="digital_output_2" class="col-sm-2 col-form-label">Dijital Çıkış 2</label>
                                        <div class="col-sm-10">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="digital_output_2" name="digital_output_2" value="1" {{ old('digital_output_2') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="digital_output_2">Açık</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Cihazı Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
