@extends('back.layouts.master')

@section('styles')
    <!-- Ekstra stil dosyaları buraya eklenebilir -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Yeni Kullanıcı Ekle</h3>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary float-right">
                            <i class="fa fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.users.store') }}" method="POST">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-sm-2 col-form-label">İsim</label>
                                <div class="col-sm-10">
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-sm-2 col-form-label">E-posta</label>
                                <div class="col-sm-10">
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="phone" class="col-sm-2 col-form-label">Telefon</label>
                                <div class="col-sm-10">
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-sm-2 col-form-label">Şifre</label>
                                <div class="col-sm-10">
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password_confirmation" class="col-sm-2 col-form-label">Şifre Tekrar</label>
                                <div class="col-sm-10">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="roles" class="col-sm-2 col-form-label">Kullanıcı Rolü</label>
                                <div class="col-sm-10">
                                    <select name="roles[]" id="roles" class="form-control select2" multiple>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Kullanıcıya atanacak rolleri seçin. Birden fazla rol seçebilirsiniz.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Kullanıcı Ekle
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Rol seçiniz"
            });
        });
    </script>
@endsection
