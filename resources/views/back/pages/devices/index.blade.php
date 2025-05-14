@extends('back.layouts.master')

@section('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <style>
        .stats-card {
            transition: all 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .battery-indicator {
            display: inline-block;
            width: 50px;
            height: 12px;
            background-color: #eee;
            border-radius: 3px;
            position: relative;
        }
        .battery-level {
            height: 100%;
            border-radius: 3px;
            background-color: #4CAF50;
        }
        .battery-low {
            background-color: #f44336;
        }
        .battery-medium {
            background-color: #FF9800;
        }
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-online {
            background-color: #4CAF50;
        }
        .status-offline {
            background-color: #f44336;
        }
        .status-maintenance {
            background-color: #FF9800;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Özet Bilgiler -->
        <div class="row mb-4">
            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 stats-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Toplam Cihaz</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-microchip fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-success shadow h-100 py-2 stats-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Gateway</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['gateways'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wifi fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-info shadow h-100 py-2 stats-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sensörler</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sensors'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-thermometer-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-warning shadow h-100 py-2 stats-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Aktüatörler</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['actuators'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-cogs fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-success shadow h-100 py-2 stats-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Çevrimiçi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['online'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-danger shadow h-100 py-2 stats-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Çevrimdışı</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['offline'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cihaz Listesi -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">LoRaWAN Cihazları</h6>
                <a href="{{ route('lorawan.devices.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Yeni Cihaz Ekle
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered" id="devices-table" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cihaz Adı</th>
                            <th>Tip</th>
                            <th>Model</th>
                            <th>EUI</th>
                            <th>Durum</th>
                            <th>Pil</th>
                            <th>Son Görülme</th>
                            <th>İşlemler</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($devices as $device)
                            <tr>
                                <td>{{ $device->id }}</td>
                                <td>{{ $device->name }}</td>
                                <td>
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
                                </td>
                                <td>{{ $device->model }}</td>
                                <td><small>{{ $device->device_eui }}</small></td>
                                <td>
                                    @if($device->status == 'online')
                                        <span><span class="status-indicator status-online"></span> Çevrimiçi</span>
                                    @elseif($device->status == 'offline')
                                        <span><span class="status-indicator status-offline"></span> Çevrimdışı</span>
                                    @else
                                        <span><span class="status-indicator status-maintenance"></span> Bakımda</span>
                                    @endif
                                </td>
                                <td>
                                    @if($device->battery_level !== null)
                                        <div class="battery-indicator">
                                            <div class="battery-level @if($device->battery_level < 20) battery-low @elseif($device->battery_level < 50) battery-medium @endif" style="width: {{ $device->battery_level }}%;"></div>
                                        </div>
                                        <small>{{ $device->battery_level }}%</small>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($device->last_seen)
                                        <small>{{ $device->last_seen->diffForHumans() }}</small>
                                    @else
                                        <small>-</small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('lorawan.devices.show', $device->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('lorawan.devices.edit', $device->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('lorawan.devices.destroy', $device->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu cihazı silmek istediğinize emin misiniz?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#devices-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json"
                },
                "order": [[ 0, "desc" ]]
            });
        });
    </script>
@endsection
