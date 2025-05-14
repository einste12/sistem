<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lorawan_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('device_type'); // Gateway, Sensor, Actuator, ESP32 vs.
            $table->string('model')->nullable(); // LPS8, UG65, ESP32 LoRa RA02, vs.
            $table->string('device_eui')->unique(); // Cihazın benzersiz tanımlayıcısı
            $table->string('application_eui')->nullable();
            $table->string('application_key')->nullable();
            $table->string('mac_address')->nullable();
            $table->text('description')->nullable();
            $table->text('configuration')->nullable(); // JSON yapıda konfigürasyon bilgileri
            $table->string('firmware_version')->nullable();
            $table->string('hardware_version')->nullable();
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('offline');
            $table->integer('battery_level')->nullable(); // Yüzde olarak pil seviyesi
            $table->timestamp('last_seen')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lorawan_devices');
    }
};
