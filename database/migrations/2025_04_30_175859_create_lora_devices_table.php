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
        Schema::create('lora_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lorawan_device_id')->constrained('lorawan_devices')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Genel cihaz meta bilgisi
            $table->string('name');
            $table->string('device_type'); // Sensör, Aktüatör, Endpoint vs.
            $table->string('model')->nullable();

            // LoRaWAN protokol kimlik bilgileri
            $table->string('dev_eui')->unique();
            $table->string('application_eui')->nullable();
            $table->string('application_key')->nullable();
            $table->integer('f_port')->nullable();

            // Ağ ve konfigürasyon
            $table->string('mac_address')->nullable();
            $table->json('configuration')->nullable();

            // Firmware / hardware sürümleri
            $table->string('firmware_version')->nullable();
            $table->string('hardware_version')->nullable();

            // Durum ve telemetri
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('offline');
            $table->integer('battery_level')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->boolean('is_active')->default(true);

            // ===== Sensör & IO Alanları =====
            // Isı / nem
            $table->float('temperature')->nullable();
            $table->float('humidity')->nullable();

            // Dijital girişler
            $table->boolean('digital_input_1')->nullable();
            $table->boolean('digital_input_2')->nullable();
            $table->boolean('digital_input_3')->nullable();

            // Dijital çıkışlar
            $table->boolean('digital_output_1')->nullable();
            $table->boolean('digital_output_2')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lora_devices');
    }
};
