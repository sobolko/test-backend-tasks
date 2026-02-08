<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExternalController extends Controller
{
    public function getCatFact()
    {
        try {
            $cached = Cache::get('cat_fact');
            if ($cached) {
                return response()->json(['data' => $cached]);
            }

            $response = Http::timeout(10)->get('https://catfact.ninja/fact');
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Кэшировать на 5 минут
                Cache::put('cat_fact', $data, 300);
                
                return response()->json(['data' => $data]);
            }

            return response()->json(['error' => 'Failed to fetch data'], 500);
            
        } catch (\Exception $e) {
            // Логируем ошибку для отладки
            \Log::error('External API error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Service temporarily unavailable'
            ], 503);
        }
    }
}