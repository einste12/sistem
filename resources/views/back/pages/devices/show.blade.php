@extends('back.layouts.master')

@section('styles')
    <style>
        .device-info-card {
            transition: all 0.3s;
        }
        .device-info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .status-badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .status-online {
            background-color: #28a745;
            color: white;
        }
        .status-offline {
            background-color: #dc3545;
            color: white;
        }
        .status-maintenance {
            background-color: #ffc107;
            color: black;
        }
        .info-label {
            font-weight: 600;
            color: #4e73df;
        }
        .battery-indicator {
            width: 100%;
            height: 20px;
            background-color: #e9ecef;
            border-radius: 4px;
            margin-top: 5px;
            position: relative;
            overflow: hidden;
        }
        .battery-level {
            height: 100%;
            border-radius: 4px;
            background-color: #4CAF50;
            transition: width 0.3s ease-in-out;
        }
        .battery-low {
            background-color: #f44336;
        }
        .battery-medium {
            background-color: #FF9800;
        }
        .device-config-section {
            background-color: #f8f9fc;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .device-actions-btn {
            margin-right: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Başlık ve Düğmeler -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ $device->name }}</h1>
            <div>
                <a href="{{ route('lorawan.devices.edit', $device->id) }}" class="btn btn-primary btn-sm device-actions-btn">
                    <i class="fas fa-edit"></i> Düzenle
                </a>
                <a href="{{ route('lorawan.devices.index') }}" class="btn btn-secondary btn-sm device-actions-btn">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
                <form action="{{ route('lorawan.devices.destroy', $device->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu cihazı silmek istediğinize emin misiniz?')">
                        <i class="fas fa-trash"></i> Sil
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Durum Bilgileri -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 device-info-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Durum</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if($device->status == 'online')
                                        <span class="status-badge status-online">Çevrimiçi</span>
                                    @elseif($device->status == 'offline')
                                        <span class="status-badge status-offline">Çevrimdışı</span>
                                    @else
                                        <span class="status-badge status-maintenance">Bakımda</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                @if($device->status == 'online')
                                    <i class="fas fa-wifi fa-2x text-gray-300"></i>
                                @elseif($device->status == 'offline')
                                    <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                @else
                                    <i class="fas fa-tools fa-2x text-gray-300"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2 device-info-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Son Görülme</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if($device->last_seen)
                                        {{ $device->last_seen->format('d.m.Y H:i:s') }}
                                        <div class="text-xs text-gray-600">{{ $device->last_seen->diffForHumans() }}</div>
                                    @else
                                        <span class="text-muted">Henüz Bağlantı Yok</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2 device-info-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pil Durumu</div>
                                @if($device->battery_level !== null)
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $device->battery_level }}%</div>
                                        </div>
                                        <div class="col">
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar
                                                @if($device->battery_level < 20) bg-danger
                                                @elseif($device->battery_level < 50) bg-warning
                                                @else bg-success @endif"
                                                     role="progressbar" style="width: {{ $device->battery_level }}%"
                                                     aria-valuenow="{{ $device->battery_level }}" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <span class="text-muted">Bilgi Yok</span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-battery-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2 device-info-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Cihaz Tipi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if($device->device_type == 'gateway')
                                        <span class="badge badge-success">Gateway</span>
                                    @elseif($device->device_type == 'sensor')
                                        <span class="badge badge-info">Sensör</span>
                                    @elseif($device->device_type == 'actuator')
                                        <span class="badge badge-warning">Aktüatör</span>
                                    @elseif($device->device_type == 'esp32')
                                        <span class="badge badge-primary">ESP32 LoRa</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $device->device_type }}</span>
                                    @endif
                                    <div class="text-xs text-gray-600 mt-1">{{ $device->model ?: 'Model belirtilmemiş' }}</div>
                                </div>
                            </div>
                            <div class="col-auto">
                                @if($device->device_type == 'gateway')
                                    <i class="fas fa-broadcast-tower fa-2x text-gray-300"></i>
                                @elseif($device->device_type == 'sensor')
                                    <i class="fas fa-thermometer-half fa-2x text-gray-300"></i>
                                @elseif($device->device_type == 'actuator')
                                    <i class="fas fa-cogs fa-2x text-gray-300"></i>
                                @elseif($device->device_type == 'esp32')
                                    <i class="fas fa-microchip fa-2x text-gray-300"></i>
                                @else
                                    <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cihaz Detayları -->
        <div class="row">
            <!-- Temel Bilgiler -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Cihaz Bilgileri</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="info-label">ID</td>
                                    <td>{{ $device->id }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Cihaz Adı</td>
                                    <td>{{ $device->name }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Device EUI</td>
                                    <td><code>{{ $device->device_eui }}</code></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Application EUI</td>
                                    <td>{{ $device->application_eui ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Application Key</td>
                                    <td>
                                        @if($device->application_key)
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="app_key" value="{{ $device->application_key }}" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" id="toggleAppKey">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="info-label">MAC Adresi</td>
                                    <td>{{ $device->mac_address ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Firmware Versiyonu</td>
                                    <td>{{ $device->firmware_version ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Hardware Versiyonu</td>
                                    <td>{{ $device->hardware_version ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Açıklama</td>
                                    <td>{{ $device->description ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Oluşturulma Tarihi</td>
                                    <td>{{ $device->created_at->format('d.m.Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Son Güncelleme</td>
                                    <td>{{ $device->updated_at->format('d.m.Y H:i:s') }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konfigürasyon Bilgileri -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Konfigürasyon Bilgileri</h6>
                    </div>
                    <div class="card-body">
                        @if($device->configuration && count((array)$device->configuration) > 0)
                            <!-- Gateway Konfigürasyonu -->
                            @if($device->device_type == 'gateway')
                                <div class="device-config-section">
                                    <h6 class="font-weight-bold">Gateway Yapılandırması</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                            <tr>
                                                <td class="info-label">Frekans Planı</td>
                                                <td>{{ isset($device->configuration['frequency_plan']) ? $device->configuration['frequency_plan'] : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">IP Adresi</td>
                                                <td>{{ isset($device->configuration['ip_address']) ? $device->configuration['ip_address'] : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Konum</td>
                                                <td>
                                                    @if(isset($device->configuration['location_lat']) && isset($device->configuration['location_lon']))
                                                        {{ $device->configuration['location_lat'] }}, {{ $device->configuration['location_lon'] }}
                                                        <a href="https://www.google.com/maps?q={{ $device->configuration['location_lat'] }},{{ $device->configuration['location_lon'] }}" target="_blank" class="btn btn-sm btn-info ml-2">
                                                            <i class="fas fa-map-marker-alt"></i> Haritada Göster
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Anten Kazancı</td>
                                                <td>{{ isset($device->configuration['antenna_gain']) ? $device->configuration['antenna_gain'] . ' dBi' : '-' }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Sensör Konfigürasyonu -->
                            @elseif($device->device_type == 'sensor')
                                <div class="device-config-section">
                                    <h6 class="font-weight-bold">Sensör Yapılandırması</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                            <tr>
                                                <td class="info-label">Sensör Tipi</td>
                                                <td>{{ isset($device->configuration['sensor_type']) ? ucfirst($device->configuration['sensor_type']) : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Ölçüm Birimi</td>
                                                <td>{{ isset($device->configuration['measurement_unit']) ? $device->configuration['measurement_unit'] : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Değer Aralığı</td>
                                                <td>
                                                    @if(isset($device->configuration['min_value']) && isset($device->configuration['max_value']))
                                                        {{ $device->configuration['min_value'] }} - {{ $device->configuration['max_value'] }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Raporlama Aralığı</td>
                                                <td>{{ isset($device->configuration['report_interval']) ? $device->configuration['report_interval'] . ' dk' : '-' }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Aktüatör Konfigürasyonu -->
                            @elseif($device->device_type == 'actuator')
                                <div class="device-config-section">
                                    <h6 class="font-weight-bold">Aktüatör Yapılandırması</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                            <tr>
                                                <td class="info-label">Aktüatör Tipi</td>
                                                <td>{{ isset($device->configuration['actuator_type']) ? ucfirst($device->configuration['actuator_type']) : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Kanal Sayısı</td>
                                                <td>{{ isset($device->configuration['channels']) ? $device->configuration['channels'] : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Varsayılan Durum</td>
                                                <td>
                                                    @if(isset($device->configuration['default_state']))
                                                        @if($device->configuration['default_state'] == 'on')
                                                            <span class="badge badge-success">Açık</span>
                                                        @else
                                                            <span class="badge badge-secondary">Kapalı</span>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <!-- Diğer Cihaz Tipleri -->
                                <table class="table table-bordered">
                                    <tbody>
                                    @foreach($device->configuration as $key => $value)
                                        <tr>
                                            <td class="info-label">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                            <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        @else
                            <div class="alert alert-info">
                                Bu cihaz için henüz bir konfigürasyon bilgisi girilmemiş.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Cihaz İşlemleri -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Cihaz İşlemleri</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Cihaz Durumu</h5>
                                        <p class="card-text">Cihazın çalışma durumunu değiştirebilirsiniz.</p>
                                        <form action="{{ route('lorawan.devices.update', $device->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="name" value="{{ $device->name }}">
                                            <input type="hidden" name="device_type" value="{{ $device->device_type }}">
                                            <input type="hidden" name="device_eui" value="{{ $device->device_eui }}">

                                            <div class="btn-group" role="group">
                                                <button type="submit" name="status" value="online" class="btn btn-success {{ $device->status == 'online' ? 'active' : '' }}">
                                                    <i class="fas fa-wifi"></i> Çevrimiçi
                                                </button>
                                                <button type="submit" name="status" value="offline" class="btn btn-danger {{ $device->status == 'offline' ? 'active' : '' }}">
                                                    <i class="fas fa-times-circle"></i> Çevrimdışı
                                                </button>
                                                <button type="submit" name="status" value="maintenance" class="btn btn-warning {{ $device->status == 'maintenance' ? 'active' : '' }}">
                                                    <i class="fas fa-tools"></i> Bakımda
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Cihaz tipine göre özel işlemler -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Hızlı İşlemler</h5>
                                        <p class="card-text">Cihaz üzerinde doğrudan işlemler gerçekleştirebilirsiniz.</p>

                                        @if($device->device_type == 'gateway')
                                            <button type="button" class="btn btn-info" id="rebootGateway">
                                                <i class="fas fa-sync"></i> Gateway'i Yeniden Başlat
                                            </button>
                                            <button type="button" class="btn btn-secondary" id="updateFirmware">
                                                <i class="fas fa-upload"></i> Firmware Güncelle
                                            </button>
                                        @elseif($device->device_type == 'actuator')
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-success" id="turnActuatorOn">
                                                    <i class="fas fa-power-off"></i> Aç
                                                </button>
                                                <button type="button" class="btn btn-danger" id="turnActuatorOff">
                                                    <i class="fas fa-power-off"></i> Kapat
                                                </button>
                                                <button type="button" class="btn btn-info" id="actuatorPulse">
                                                    <i class="fas fa-bolt"></i> Anlık Tetikle
                                                </button>
                                            </div>
                                        @elseif($device->device_type == 'sensor')
                                            <button type="button" class="btn btn-info" id="requestReading">
                                                <i class="fas fa-redo"></i> Anlık Değer Oku
                                            </button>
                                            <button type="button" class="btn btn-secondary" id="updateReportInterval">
                                                <i class="fas fa-clock"></i> Rapor Aralığını Değiştir
                                            </button>
                                        @else
                                            <div class="alert alert-info">
                                                Bu cihaz tipi için tanımlanmış hızlı işlem bulunmamaktadır.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Veriler (Sensörler için) -->
        @if($device->device_type == 'sensor')
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Son Ölçüm Değerleri</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Bu cihazın son ölçüm değerleri buraya gelecektir. Şu anda veri bulunmamaktadır.
                            </div>

                            <!-- Burada gelecekte sensör verilerini gösterecek bir grafik veya tablo eklenebilir -->
                            <div class="text-center py-4">
                                <i class="fas fa-chart-line fa-4x text-gray-300 mb-3"></i>
                                <p>Bu cihazdan henüz veri alınmamıştır. Veri geldikçe burada görüntülenecektir.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Application Key göster/gizle
            $('#toggleAppKey').click(function() {
                var $input = $('#app_key');
                var $icon = $(this).find('i');

                if ($input.attr('type') === 'password') {
                    $input.attr('type', 'text');
                    $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    $input.attr('type', 'password');
                    $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Cihaz tipine özel butonların işlemleri (gerçek işlevsellik burada eklenebilir)
            $('#rebootGateway, #updateFirmware, #turnActuatorOn, #turnActuatorOff, #actuatorPulse, #requestReading, #updateReportInterval').click(function() {
                var buttonId = $(this).attr('id');
                var actionText = '';

                switch(buttonId) {
                    case 'rebootGateway':
                        actionText = 'Gateway yeniden başlatılıyor...';
                        break;
                    case 'updateFirmware':
                        actionText = 'Firmware güncelleme işlemi başlatılıyor...';
                        break;
                    case 'turnActuatorOn':
                        actionText = 'Aktüatör açılıyor...';
                        break;
                    case 'turnActuatorOff':
                        actionText = 'Aktüatör kapatılıyor...';
                        break;
                    case 'actuatorPulse':
                        actionText = 'Aktüatör tetikleniyor...';
                        break;
                    case 'requestReading':
                        actionText = 'Sensörden anlık değer isteniyor...';
                        break;
                    case 'updateReportInterval':
                        actionText = 'Rapor aralığı güncelleniyor...';
                        break;
                }

                // Bu kısım backend bağlantısı olmadığı için sadece bilgi mesajı gösterir
                // Gerçek implementasyonda bu işlemler API çağrıları ile gerçekleştirilir
                alert(actionText + '\n\nBu özellik henüz uygulanmamıştır ve sadece gösterim amaçlıdır.');
            });
        });
    </script>
@endsection
