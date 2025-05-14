@extends('back.layouts.master')

@section('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title float-left">Kullanıcı Listesi</h3>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary float-right">
                            <i class="fa fa-plus"></i> Yeni Kullanıcı Ekle
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="users-table" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>İsim</th>
                                    <th>E-posta</th>
                                    <th>Telefon</th>
                                    <th>Rol</th>
                                    <th>Oluşturulma Tarihi</th>
                                    <th>İşlemler</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?: 'Belirtilmemiş' }}</td>
                                        <td>
                                            @if($user->roles->count() > 0)
                                                <span>{{ $user->roles->first()->name }}</span>
                                            @else
                                                <span class="badge badge-warning">Rol Atanmamış</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-edit"></i> Düzenle
                                            </a>

                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">
                                                    <i class="fa fa-trash"></i> Sil
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
        </div>
    </div>
@endsection

@section('js')
    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json"
                },
                "order": [[ 0, "desc" ]]
            });
        });
    </script>
@endsection
