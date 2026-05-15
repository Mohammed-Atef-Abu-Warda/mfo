<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DiscordService
{
    public function sendSignal($type, $price, $score, $strength)
    {
        $url = env('DISCORD_WEBHOOK_URL');
        $color = ($type == 'BUY') ? 3066993 : 15158332; // أخضر للشراء، أحمر للبيع

        return Http::post($url, [
            "embeds" => [[
                "title" => "🚀 إشارة دخول جديدة: " . $type,
                "description" => "تم رصد فرصة قناص على زوج PAXG/USDT",
                "color" => $color,
                "fields" => [
                    ["name" => "💰 السعر الحالي", "value" => "$" . $price, "inline" => true],
                    ["name" => "📊 قوة الخبر", "value" => $score, "inline" => true],
                    ["name" => "💪 نوع الإشارة", "value" => $strength, "inline" => false],
                ],
                "footer" => ["text" => "رادار الذهب الذكي - " . now()->format('Y-m-d H:i')]
            ]]
        ]);
    }

    public function sendAlert($message)
    {
        return Http::post(env('DISCORD_WEBHOOK_URL'), [
            "content" => "🔔 **تنبيه مبكر:** " . $message
        ]);
    }
}