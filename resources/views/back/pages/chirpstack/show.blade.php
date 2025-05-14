@extends('back.layouts.master')

@section('styles')
    <style>
        .info-card {
            transition: all 0.3s;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .device-info {
            margin-bottom: 10px;
        }
        .device-info strong {
            color: #4e73df;
        }
        .frame-log {
            font-family: monospace;
            background-color: #f8f9fc;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .downlink-form {
            background-color: #f8f9fc;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .hex-data {
            font-family: monospace;
            background-color: #e9ecef;
            padding: 3px 6px;
            border-radius: 3px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ $deviceData['name'] }} - ChirpStack Detayları</h1>
            <div>
                <a href="{{ route('admin.chirpstack.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <!-- Cihaz Bilgileri -->
            <div class="col-lg-6">
                <div class="card shadow mb-4 info-card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Cihaz Bilgileri</h6>
                    </div>
                    <div class="card-body">
                        <div class="device-info">
                            <strong>DevEUI:</strong> <code>{{ $deviceData['devEui'] }}</code>
                        </div>
                        <div class="device-info">
                            <strong>Application ID:</strong> {{ $deviceData['applicationId'] ?? '-' }}
                        </div>
                        <div class="device-info">
                            <strong>Device Profile ID:</strong> {{ $deviceData['deviceProfileId'] ?? '-' }}
                        </div>
                        <div class="device-info">
                            <strong>Açıklama:</strong> {{ $deviceData['description'] ?? 'Açıklama yok' }}
                        </div>
                        <div class="device-info">
                            <strong>Durum:</strong>
                            @if($deviceData['isDisabled'] ?? false)
                                <span class="badge badge-danger">Devre Dışı</span>
                            @else
                                <span class="badge badge-success">Aktif</span>
                            @endif
                        </div>
                        @if(isset($deviceData['lastSeenAt']))
                            <div class="device-info">
                                <strong>Son Görülme:</strong>
                                {{ \Carbon\Carbon::parse($deviceData['lastSeenAt'])->format('d.m.Y H:i:s') }}
                                <small class="text-muted">({{ \Carbon\Carbon::parse($deviceData['lastSeenAt'])->diffForHumans() }})</small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Activation Bilgileri -->
                @if($activation)
                    <div class="card shadow mb-4 info-card">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Activation Bilgileri</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($activation['deviceActivation']))
                                <div class="device-info">
                                    <strong>DevAddr:</strong> <code>{{ $activation['deviceActivation']['devAddr'] ?? '-' }}</code>
                                </div>
                                <div class="device-info">
                                    <strong>F-Count Up:</strong> {{ $activation['deviceActivation']['fCntUp'] ?? 0 }}
                                </div>
                                <div class="device-info">
                                    <strong>F-Count Down:</strong> {{ $activation['deviceActivation']['fCntDown'] ?? 0 }}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Cihaz henüz aktive edilmemiş.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Frame Logs -->
            <div class="col-lg-6">
                <div class="card shadow mb-4 info-card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Son Frame Logları</h6>
                    </div>
                    <div class="card-body">
                        @if($frames && isset($frames['result']))
                            @foreach($frames['result'] as $frame)
                                <div class="frame-log">
                                    <div class="mb-1">
                                        <strong>{{ \Carbon\Carbon::parse($frame['publishedAt'])->format('d.m.Y H:i:s') }}</strong>
                                    </div>
                                    <div class="mb-1">
                                        <span class="badge badge-{{ $frame['txInfo']['modulation']['lora']['spreadingFactor'] ?? 12 > 10 ? 'warning' : 'success' }}">
                                            {{ $frame['txInfo']['modulation']['lora']['spreadingFactor'] ?? 12 }}
                                        </span>
                                        {{ $frame['txInfo']['frequency'] ?? 'N/A' }} MHz
                                    </div>
                                    @if(isset($frame['rxInfo']) && count($frame['rxInfo']) > 0)
                                        <div>
                                            <strong>RSSI:</strong> {{ $frame['rxInfo'][0]['rssi'] ?? 'N/A' }} dBm
                                            <strong>SNR:</strong> {{ $frame['rxInfo'][0]['snr'] ?? 'N/A' }} dB
                                        </div>
                                    @endif
                                    @if(isset($frame['data']))
                                        <div>
                                            <strong>Data:</strong> <span class="hex-data">{{ $frame['data'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                Frame log bulunmuyor.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Downlink Mesaj Gönderme -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4 info-card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Downlink Mesaj Gönder</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.chirpstack.downlink', $localDevice->id) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="data">Downlink Verisi (Hex)</label>
                                        <input type="text" class="form-control" id="data" name="data" required
                                               placeholder="Örn: 01FF0120" pattern="[0-9A-Fa-f]+">
                                        <small class="form-text text-muted">Hexadecimal formatında veri girin (örn: 01FF0120)</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="fport">F-Port</label>
                                        <input type="number" class="form-control" id="fport" name="fport" value="10" min="1" max="223" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-paper-plane"></i> Gönder
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Örnek Downlink Komutları -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="font-weight-bold">Örnek Komutlar:</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6 class="card-title">LED Kontrol</h6>
                                                <p class="card-text">
                                                    <code>010001</code> - LED Aç<br>
                                                    <code>010000</code> - LED Kapat
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6 class="card-title">Röle Kontrol</h6>
                                                <p class="card-text">
                                                    <code>020101</code> - Röle 1 Aç<br>
                                                    <code>020100</code> - Röle 1 Kapat
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6 class="card-title">Sensör Okuma</h6>
                                                <p class="card-text">
                                                    <code>03</code> - Sıcaklık Oku<br>
                                                    <code>04</code> - Nem Oku
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
