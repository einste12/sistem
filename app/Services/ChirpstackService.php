<?php

namespace App\Services;

use Grpc\BaseStub;
use Grpc\UnaryCall;
use Google\Protobuf\Empty;
use Illuminate\Support\Facades\Log;

class ChirpstackService
{
    private $channel;
    private $client;
    private $apiKey;

    public function __construct()
    {
        $host = env('CHIRPSTACK_GRPC_HOST', 'localhost:8080');
        $this->apiKey = env('CHIRPSTACK_API_KEY', '');

        // gRPC channel oluştur
        $this->channel = new \Grpc\Channel($host, [
            'credentials' => \Grpc\ChannelCredentials::createInsecure(),
        ]);

        // DeviceService client'ı oluştur
        $this->client = new DeviceServiceClient($this->channel);
    }

    /**
     * gRPC ile cihazları listele
     */
    public function listDevices($limit = 50, $offset = 0, $applicationId = null)
    {
        try {
            // Request oluştur
            $request = new ListDevicesRequest();
            $request->setLimit($limit);
            $request->setOffset($offset);

            if ($applicationId) {
                $request->setApplicationId($applicationId);
            }

            // Metadata (API key için)
            $metadata = [
                'authorization' => ['Bearer ' . $this->apiKey]
            ];

            // gRPC call
            list($response, $status) = $this->client->List($request, $metadata)->wait();

            if ($status->code !== \Grpc\STATUS_OK) {
                Log::error('gRPC error: ' . $status->details);
                return null;
            }

            // Response'u dönüştür
            $devices = [];
            foreach ($response->getResult() as $device) {
                $devices[] = $this->deviceToArray($device);
            }

            return [
                'totalCount' => $response->getTotalCount(),
                'result' => $devices
            ];

        } catch (\Exception $e) {
            Log::error('gRPC exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tek bir cihazı getir
     */
    public function getDevice($devEui)
    {
        try {
            $request = new GetDeviceRequest();
            $request->setDevEui($devEui);

            $metadata = [
                'authorization' => ['Bearer ' . $this->apiKey]
            ];

            list($response, $status) = $this->client->Get($request, $metadata)->wait();

            if ($status->code !== \Grpc\STATUS_OK) {
                Log::error('gRPC error: ' . $status->details);
                return null;
            }

            return [
                'device' => $this->deviceToArray($response->getDevice()),
                'lastSeenAt' => $response->getLastSeenAt()?->toDateTime(),
                'deviceStatus' => $this->deviceStatusToArray($response->getDeviceStatus())
            ];

        } catch (\Exception $e) {
            Log::error('gRPC exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Device object'ini array'e dönüştür
     */
    private function deviceToArray($device)
    {
        return [
            'devEui' => $device->getDevEui(),
            'name' => $device->getName(),
            'description' => $device->getDescription(),
            'applicationId' => $device->getApplicationId(),
            'deviceProfileId' => $device->getDeviceProfileId(),
            'skipFcntCheck' => $device->getSkipFcntCheck(),
            'isDisabled' => $device->getIsDisabled(),
            'variables' => $device->getVariables(),
            'tags' => $device->getTags(),
            'joinEui' => $device->getJoinEui()
        ];
    }

    /**
     * DeviceStatus object'ini array'e dönüştür
     */
    private function deviceStatusToArray($status)
    {
        if (!$status) {
            return null;
        }

        return [
            'margin' => $status->getMargin(),
            'externalPowerSource' => $status->getExternalPowerSource(),
            'batteryLevel' => $status->getBatteryLevel()
        ];
    }
}
