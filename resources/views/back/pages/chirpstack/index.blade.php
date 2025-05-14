@extends('back.layouts.master')

@section('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <style>
        .chirpstack-status {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-active {
            background-color: #28a745;
        }
        .status-inactive {
            background-color: #dc3545;
        }
        .device-eui {
            font-family: monospace;
            background-color: #f8f9fc;
            padding: 3px 8px;
            border-radius: 3px;
        }
        .last-seen {
            color: #5a5c69;
            font-size: 0.9em;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">ChirpStack Cihazları</h1>
            <div>
                <form action="{{ route('admin.chirpstack.sync') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info btn-sm mr-2">
                        <i class="fas fa-sync"></i> Senkronize Et
                    </button>
                </form>
                <a href="{{ route('admin.chirpstack.web') }}" class="btn btn-primary btn-sm" target="_blank">
                    <i class="fas fa-external-link-alt"></i> ChirpStack UI
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

        <!-- Cihaz Listesi -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">ChirpStack'ten Alınan Cihazlar</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="devices-table" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Durum</th>
                            <th>Cihaz Adı</th>
                            <th>DevEUI</th>
                            <th>Cihaz Tipi</th>
                            <th>Son Görülme</th>
                            <th>İşlemler</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($localDevices as $device)
                            <tr>
                                <td>
                                    <span class="chirpstack-status {{ $device->is_active ? 'status-active' : 'status-inactive' }}"></span>
                                    {{ $device->is_active ? 'Aktif' : 'Pasif' }}
                                </td>
                                <td>{{ $device->name }}</td>
                                <td><code class="device-eui">{{ $device->dev_eui }}</code></td>
                                <td>
                                    @if($device->device_type == 'sensor')
                                        <span class="badge badge-info">Sensör</span>
                                    @elseif($device->device_type == 'actuator')
                                        <span class="badge badge-warning">Aktüatör</span>
                                    @elseif($device->device_type == 'gateway')
                                        <span class="badge badge-success">Gateway</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $device->device_type }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($device->last_seen)
                                        <span class="last-seen">{{ $device->last_seen->diffForHumans() }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.chirpstack.show', $device->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detaylar
                                    </a>
                                    <a href="{{ route('admin.lora.devices.show', $device->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-cog"></i> Ayarlar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bilgi Kartları -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">ChirpStack Bağlantısı</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>API URL:</strong> {{ env('CHIRPSTACK_API_URL') }}</p>
                        <p><strong>Organization ID:</strong> {{ env('CHIRPSTACK_ORGANIZATION_ID') }}</p>
                        <p><strong>API Key:</strong> {{ substr(env('CHIRPSTACK_API_KEY'), 0, 10) }}...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">İstatistikler</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Toplam Cihaz:</strong> {{ $localDevices->count() }}</p>
                        <p><strong>Aktif Cihaz:</strong> {{ $localDevices->where('is_active', true)->count() }}</p>
                        <p><strong>Sensör:</strong> {{ $localDevices->where('device_type', 'sensor')->count() }}</p>
                        <p><strong>Aktüatör:</strong> {{ $localDevices->where('device_type', 'actuator')->count() }}</p>
                    </div>
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
                "order": [[ 1, "asc" ]]
            });
        });
    </script>
@endsection
