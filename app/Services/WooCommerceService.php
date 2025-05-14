<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WooCommerceService
{
    protected $apiUrl;
    protected $consumerKey;
    protected $consumerSecret;

    public function __construct()
    {
        $this->apiUrl = env('WOOCOMMERCE_API_URL', 'https://oyunkampta.com/wp-json/wc/v3');
        $this->consumerKey = env('WOOCOMMERCE_CONSUMER_KEY', 'ck_33fd4920c976eafdc9ba959dae3e869e922f7c27');
        $this->consumerSecret = env('WOOCOMMERCE_CONSUMER_SECRET', 'cs_1045de4488733b788b177ac9c69c4f83373301a1');
    }

    /**
     * Get orders from WooCommerce API
     *
     * @param string $status Order statuses to fetch
     * @param int $perPage Number of orders per page
     * @return \Illuminate\Support\Collection
     */
    public function getOrders($status = "completed,processing,on-hold,partially-paid", $perPage = 100)
    {
        $page = 1;
        $allOrders = collect([]);

        try {
            do {
                // Query parametreleri ile kimlik doğrulama
                $queryParams = [
                    'status' => $status,
                    'per_page' => $perPage,
                    'page' => $page,
                    'consumer_key' => $this->consumerKey,
                    'consumer_secret' => $this->consumerSecret
                ];

                Log::info("Fetching WooCommerce orders page: " . $page);

                // OAuth yerine API anahtarlarını URL parametresi olarak kullan
                $response = Http::get("{$this->apiUrl}/orders", $queryParams);

                if ($response->failed()) {
                    $error = $response->body();
                    Log::error("WooCommerce API error response: " . $error);
                    throw new \Exception('WooCommerce API request failed: ' . $error);
                }

                $orders = $response->json();
                $allOrders = $allOrders->merge($orders);
                $page++;

            } while (count($orders) > 0);

            return $allOrders;
        } catch (\Exception $e) {
            Log::error('WooCommerce API Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
