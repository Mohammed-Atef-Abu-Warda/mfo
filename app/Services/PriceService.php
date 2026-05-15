<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PriceService
{
    public function getAdvancedAnalysis()
    {
        try {
            // جلب البيانات من Binance (فريم ساعة و 15 دقيقة)
            $h1Response = Http::withoutVerifying()->get("https://api.binance.com/api/v3/klines", [
                'symbol' => 'PAXGUSDT', 'interval' => '1h', 'limit' => 50
            ]);

            $m15Response = Http::withoutVerifying()->get("https://api.binance.com/api/v3/klines", [
                'symbol' => 'PAXGUSDT', 'interval' => '15m', 'limit' => 50
            ]);

            if (!$h1Response->successful() || !$m15Response->successful()) return null;

            $h1Data = collect($h1Response->json());
            $m15Data = collect($m15Response->json());

            $h1Closes = $h1Data->map(fn($k) => (float)$k[4]);
            $m15Closes = $m15Data->map(fn($k) => (float)$k[4]);

            $currentPrice = $m15Closes->last();

            // حساب المؤشرات
            $bb = $this->calculateBollingerBands($m15Closes);
            $volatility = $this->calculateVolatility($m15Closes->take(-10));
            $rsi = $this->calculateRSI($m15Closes->take(-14)->toArray());

            return [
                'price' => $currentPrice,
                'hourly_trend' => $currentPrice > $h1Closes->take(-20)->average() ? 'UP' : 'DOWN',
                'm15_trend' => $currentPrice > $m15Closes->take(-20)->average() ? 'UP' : 'DOWN',
                'bb_upper' => round($bb['upper'], 2),
                'bb_lower' => round($bb['lower'], 2),
                'rsi' => round($rsi, 2),
                'volatility' => round($volatility, 2),
                'is_market_dead' => $volatility < 0.6 // إذا تحرك الذهب أقل من 0.6 دولار في 15 دقيقة فالسوق ميت
            ];

        } catch (\Exception $e) {
            return null;
        }
    }

    private function calculateBollingerBands($data)
    {
        $last20 = $data->take(-20);
        $ma = $last20->average();
        $variance = $last20->map(fn($x) => pow($x - $ma, 2))->average();
        $stdDev = sqrt($variance);

        return [
            'upper' => $ma + ($stdDev * 2),
            'lower' => $ma - ($stdDev * 2)
        ];
    }

    private function calculateVolatility($data)
    {
        $mean = $data->average();
        $variance = $data->map(fn($x) => pow($x - $mean, 2))->average();
        return sqrt($variance);
    }

    private function calculateRSI(array $prices)
    {
        $prices = array_values($prices); // التأكد من ترتيب المفاتيح
        if (count($prices) < 14) return 50; // قيمة افتراضية في حال نقص البيانات

        $gains = 0; $losses = 0;
        for ($i = 1; $i < count($prices); $i++) {
            $diff = $prices[$i] - $prices[$i-1];
            if ($diff > 0) $gains += $diff; else $losses += abs($diff);
        }
        
        if ($losses == 0) return 100;
        $rs = $gains / $losses;
        return round(100 - (100 / (1 + $rs)), 2);
    }

    // لتجنب أخطاء لوحة التحكم، نوجه الدالة القديمة للجديدة
    public function getTechnicalAnalysis()
    {
        return $this->getAdvancedAnalysis();
    }
}