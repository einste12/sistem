@extends('back.layouts.master')

@section('styles')
    <style>
        .form-section {
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f8f9fc;
        }
        .form-section-title {
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .device-card {
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .device-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .device-card .card-header {
            padding: 0.5rem 1rem;
        }
        .device-card .card-body {
            padding: 0.75rem;
        }
        .selected-device {
            border-left: 4px solid #4e73df;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #4e73df;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            margin-right: 15px;
        }
        .section-tabs .nav-link {
            padding: 10px 15px;
            border-radius: 0;
            border: none;
            color: #5a5c69;
            font-weight: 600;
        }
        .section-tabs .nav-link.active {
            color: #4e73df;
            background-color: #f8f9fc;
            border-bottom: 3px solid #4e73df;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Kullanıcı Cihaz Atamalarını Düzenle</h6>
                        <a href="{{ route('admin.device-assignments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
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

                        <!-- Kullanıcı Bilgisi -->
                        <div class="form-section">
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="mb-1">{{ $user->name }}</h4>
                                    <p class="mb-0 text-muted">{{ $user->email }}</p>
                                    <small class="text-muted">Şu anda {{ $assignedDevices->count() }} cihaz atanmış</small>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.device-assignments.update', $user->id) }}" method="POST" id="assignment-form">
                            @csrf
                            @method('PUT')

                            <!-- Cihaz Seçimi Sekmeleri -->
                            <ul class="nav nav-tabs section-tabs mb-3" id="deviceTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="assigned-tab" data-toggle="tab" href="#assigned" role="tab" aria-controls="assigned" aria-selected="true">
                                        <i class="fas fa-check-circle mr-1"></i> Atanmış Cihazlar ({{ $assignedDevices->count() }})
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="unassigned-tab" data-toggle="tab" href="#unassigned" role="tab" aria-controls="unassigned" aria-selected="false">
                                        <i class="fas fa-plus-circle mr-1"></i> Atanabilecek Cihazlar ({{ $unassignedDevices->count() }})
                                    </a>
                                </li>
                            </ul>

                            <!-- Cihaz Sekme İçerikleri -->
                            <div class="tab-content" id="deviceTabsContent">
                                <!-- Atanmış Cihazlar -->
                                <div class="tab-pane fade show active" id="assigned" role="tabpanel" aria-labelledby="assigned-tab">
                                    <div class="form-section">
                                        <div class="form-section-title d-flex justify-content-between">
                                            <span>Kullanıcıya Atanmış Cihazlar</span>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="unselect-all">
                                                    <i class="fas fa-times-circle"></i> Tümünü Kaldır
                                                </button>
                                            </div>
                                        </div>

                                        @if($assignedDevices->isNotEmpty())
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="assigned-search" placeholder="Atanmış cihazlarda ara...">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                @foreach($assignedDevices as $device)
                                                    <div class="col-md-4 device-item assigned" data-name="{{ strtolower($device->name) }}" data-type="{{ strtolower($device->device_type) }}" data-eui="{{ strtolower($device->dev_eui) }}">
                                                        <div class="card device-card selected-device">
                                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                                <h6 class="mb-0 text-primary">{{ $device->name }}</h6>
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input device-checkbox" id="device-{{ $device->id }}" name="device_ids[]" value="{{ $device->id }}" checked>
                                                                    <label class="custom-control-label" for="device-{{ $device->id }}"></label>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="d-flex justify-content-between mb-2">
                                                                <span>
                                                                    @if($device->device_type == 'sensor')
                                                                        <span class="badge badge-info">Sensör</span>
                                                                    @elseif($device->device_type == 'actuator')
                                                                        <span class="badge badge-warning">Aktüatör</span>
                                                                    @else
                                                                        <span class="badge badge-secondary">{{ $device->device_type }}</span>
                                                                    @endif
                                                                </span>
                                                                    <span>
                                                                    @if($device->status == 'online')
                                                                            <span class="badge badge-success">Çevrimiçi</span>
                                                                        @elseif($device->status == 'offline')
                                                                            <span class="badge badge-danger">Çevrimdışı</span>
                                                                        @else
                                                                            <span class="badge badge-warning">Bakımda</span>
                                                                        @endif
                                                                </span>
                                                                </div>
                                                                <div class="small">
                                                                    <strong>Model:</strong> {{ $device->model ?: 'Belirtilmemiş' }}
                                                                </div>
                                                                <div class="small">
                                                                    <strong>DEV EUI:</strong> <code>{{ $device->dev_eui }}</code>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="row mt-3 no-results assigned-no-results" style="display: none;">
                                                <div class="col-12">
                                                    <div class="alert alert-warning">
                                                        Arama kriterlerine uygun atanmış cihaz bulunamadı.
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                Bu kullanıcıya henüz cihaz atanmamış.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Atanabilecek Cihazlar -->
                                <div class="tab-pane fade" id="unassigned" role="tabpanel" aria-labelledby="unassigned-tab">
                                    <div class="form-section">
                                        <div class="form-section-title d-flex justify-content-between">
                                            <span>Atanabilecek Cihazlar</span>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="select-all">
                                                    <i class="fas fa-check-circle"></i> Tümünü Seç
                                                </button>
                                            </div>
                                        </div>

                                        @if($unassignedDevices->isNotEmpty())
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="unassigned-search" placeholder="Atanabilecek cihazlarda ara...">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                @foreach($unassignedDevices as $device)
                                                    <div class="col-md-4 device-item unassigned" data-name="{{ strtolower($device->name) }}" data-type="{{ strtolower($device->device_type) }}" data-eui="{{ strtolower($device->dev_eui) }}">
                                                        <div class="card device-card">
                                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                                <h6 class="mb-0 text-primary">{{ $device->name }}</h6>
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input device-checkbox" id="device-{{ $device->id }}" name="device_ids[]" value="{{ $device->id }}">
                                                                    <label class="custom-control-label" for="device-{{ $device->id }}"></label>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="d-flex justify-content-between mb-2">
                                                                <span>
                                                                    @if($device->device_type == 'sensor')
                                                                        <span class="badge badge-info">Sensör</span>
                                                                    @elseif($device->device_type == 'actuator')
                                                                        <span class="badge badge-warning">Aktüatör</span>
                                                                    @else
                                                                        <span class="badge badge-secondary">{{ $device->device_type }}</span>
                                                                    @endif
                                                                </span>
                                                                    <span>
                                                                    @if($device->status == 'online')
                                                                            <span class="badge badge-success">Çevrimiçi</span>
                                                                        @elseif($device->status == 'offline')
                                                                            <span class="badge badge-danger">Çevrimdışı</span>
                                                                        @else
                                                                            <span class="badge badge-warning">Bakımda</span>
                                                                        @endif
                                                                </span>
                                                                </div>
                                                                <div class="small">
                                                                    <strong>Model:</strong> {{ $device->model ?: 'Belirtilmemiş' }}
                                                                </div>
                                                                <div class="small">
                                                                    <strong>DEV EUI:</strong> <code>{{ $device->dev_eui }}</code>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="row mt-3 no-results unassigned-no-results" style="display: none;">
                                                <div class="col-12">
                                                    <div class="alert alert-warning">
                                                        Arama kriterlerine uygun atanabilecek cihaz bulunamadı.
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                Atanabilecek cihaz bulunmamaktadır.
                                            </div>
                                        @endif
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

@section('js')
    <script>
        $(document).ready(function() {
            // Cihaz seçildiğinde stil değişimi
            $('.device-checkbox').change(function() {
                if($(this).is(':checked')) {
                    $(this).closest('.device-card').addClass('selected-device');
                } else {
                    $(this).closest('.device-card').removeClass('selected-device');
                }
            });

            // Atanmış cihazlar arama fonksiyonu
            $('#assigned-search').on('keyup', function() {
                let searchText = $(this).val().toLowerCase();
                let visibleCount = 0;

                $('.device-item.assigned').each(function() {
                    let deviceName = $(this).data('name');
                    let deviceType = $(this).data('type');
                    let deviceEui = $(this).data('eui');

                    if (deviceName.includes(searchText) || deviceType.includes(searchText) || deviceEui.includes(searchText)) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                // Sonuç yoksa mesaj göster
                if (visibleCount === 0) {
                    $('.assigned-no-results').show();
                } else {
                    $('.assigned-no-results').hide();
                }
            });

            // Atanabilecek cihazlar arama fonksiyonu
            $('#unassigned-search').on('keyup', function() {
                let searchText = $(this).val().toLowerCase();
                let visibleCount = 0;

                $('.device-item.unassigned').each(function() {
                    let deviceName = $(this).data('name');
                    let deviceType = $(this).data('type');
                    let deviceEui = $(this).data('eui');

                    if (deviceName.includes(searchText) || deviceType.includes(searchText) || deviceEui.includes(searchText)) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                // Sonuç yoksa mesaj göster
                if (visibleCount === 0) {
                    $('.unassigned-no-results').show();
                } else {
                    $('.unassigned-no-results').hide();
                }
            });

            // Tümünü seç butonu
            $('#select-all').click(function() {
                $('.device-item.unassigned:visible .device-checkbox').prop('checked', true).change();
            });

            // Tümünü kaldır butonu
            $('#unselect-all').click(function() {
                $('.device-item.assigned:visible .device-checkbox').prop('checked', false).change();
            });
        });
    </script>
@endsection
