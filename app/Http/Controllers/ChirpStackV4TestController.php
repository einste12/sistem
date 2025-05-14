<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ChirpStackV4TestController extends Controller
{
    public function test()
    {
        $apiUrl = env('CHIRPSTACK_API_URL');
        $apiKey = env('CHIRPSTACK_API_KEY');

        echo "<h1>ChirpStack v4 gRPC-Web Gateway Test</h1>";
        echo "<pre style='background: #f4f4f4; padding: 20px;'>";

        echo "API URL: $apiUrl\n";
        echo "API Key: " . substr($apiKey, 0, 30) . "...\n\n";

        // Test 1: Applications
        try {
            echo "=== Testing Applications (gRPC-Web) ===\n";
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($apiUrl . '/api.ApplicationService/List', [
                    'limit' => 100,
                    'offset' => 0
                ]);

            echo "Status: " . $response->status() . "\n";

            if ($response->successful()) {
                echo "SUCCESS! ✅\n";
                $data = $response->json();
                echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
            } else {
                echo "Failed ❌\n";
                echo "Body: " . $response->body() . "\n";
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }

        echo "\n";

        // Test 2: Devices
        try {
            echo "=== Testing Devices (gRPC-Web) ===\n";
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($apiUrl . '/api.DeviceService/List', [
                    'limit' => 100,
                    'offset' => 0
                ]);

            echo "Status: " . $response->status() . "\n";

            if ($response->successful()) {
                echo "SUCCESS! ✅\n";
                $data = $response->json();
                echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
            } else {
                echo "Failed ❌\n";
                echo "Body: " . $response->body() . "\n";
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }

        echo "</pre>";

        // Alternative endpoints to try
        echo "<h2>Alternative Endpoints</h2>";
        echo "<pre style='background: #f4f4f4; padding: 20px;'>";

        $endpoints = [
            '/api/applications' => 'GET',
            '/api/devices' => 'GET',
            '/api.ApplicationService/List' => 'POST',
            '/api.DeviceService/List' => 'POST',
            '/chirpstack.ApplicationService/List' => 'POST',
            '/chirpstack.DeviceService/List' => 'POST',
        ];

        foreach ($endpoints as $endpoint => $method) {
            echo "\nTesting: $apiUrl$endpoint ($method)\n";
            try {
                $headers = [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept' => 'application/json'
                ];

                if ($method === 'POST') {
                    $headers['Content-Type'] = 'application/json';
                    $response = Http::timeout(5)
                        ->withoutVerifying()
                        ->withHeaders($headers)
                        ->post($apiUrl . $endpoint, ['limit' => 10]);
                } else {
                    $response = Http::timeout(5)
                        ->withoutVerifying()
                        ->withHeaders($headers)
                        ->get($apiUrl . $endpoint);
                }

                echo "Status: " . $response->status() . "\n";

                if ($response->status() !== 404) {
                    echo "Response: " . substr($response->body(), 0, 200) . "\n";
                }
            } catch (\Exception $e) {
                echo "Error: " . substr($e->getMessage(), 0, 100) . "\n";
            }
        }

        echo "</pre>";
    }
}
