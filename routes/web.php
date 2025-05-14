<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Back\DashboardController;
use App\Http\Controllers\Back\ProfileController;
use App\Http\Controllers\Back\DevicesController;
use App\Http\Controllers\Back\LorawanDeviceController;
use App\Http\Controllers\Back\LoraDeviceController;
use App\Http\Controllers\ChirpStackV4TestController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/', [AuthController::class, 'login_post'])->name('login.post');
});


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('devices', [DevicesController::class, 'index'])->name('devices.index');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});


Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Back\UserController::class);
    Route::resource('device-assignments', \App\Http\Controllers\Back\DeviceAssignmentController::class);
    Route::resource('lora/devices', \App\Http\Controllers\Back\LoraDeviceController::class, ['as' => 'lora']);
    Route::post('lora/devices/{device}/toggle-output', [\App\Http\Controllers\Back\LoraDeviceController::class, 'toggleOutput'])->name('lora.devices.toggle-output');
});

Route::middleware(['auth','role:Admin'])->prefix('lorawan')->name('lorawan.')->group(function () {
    Route::resource('devices', LorawanDeviceController::class);
});



Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    // ChirpStack entegrasyonu
    Route::get('chirpstack', [\App\Http\Controllers\ChirpstackController::class, 'index'])->name('chirpstack.index');
    Route::get('chirpstack/web', [\App\Http\Controllers\ChirpstackController::class, 'webInterface'])->name('chirpstack.web');
    Route::post('chirpstack/sync', [\App\Http\Controllers\ChirpstackController::class, 'sync'])->name('chirpstack.sync');
    Route::get('chirpstack/{id}', [\App\Http\Controllers\ChirpstackController::class, 'show'])->name('chirpstack.show');
    Route::post('chirpstack/{id}/downlink', [\App\Http\Controllers\ChirpstackController::class, 'sendDownlink'])->name('chirpstack.downlink');
});




Route::middleware(['auth', 'role:Personel|Admin'])->prefix('user')->name('user.')->group(function () {
    Route::get('my-devices', [\App\Http\Controllers\Back\DeviceAssignmentController::class, 'userDevices'])->name('my-devices');
});




Route::get('admin/chirpstack-v4-test', [ChirpStackV4TestController::class, 'test'])->name('admin.chirpstack.v4test');

