<?php

namespace App\Http\Controllers;
use App\Services\PriceService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

public function testDiscord() {
    Http::post(env('DISCORD_WEBHOOK_URL'), [
        "content" => "🚀 تم تفعيل رادار الذهب بنجاح! الروبوت الآن متصل بالقناة."
    ]);
}

    public function index(PriceService $priceService)
    {
        $techData = $priceService->getTechnicalAnalysis();
        $news = \App\Models\News::latest()->take(5)->get();
        
        // جلب كل الإشارات للجدول
        $signals = \App\Models\Signal::latest()->take(10)->get();
        
        // جلب آخر إشارة واحدة فقط للمبة التنبيه
        $latest_signal = \App\Models\Signal::latest()->first();

        return view('dashboard', [
            'price' => $techData,
            'news' => $news,
            'signals' => $signals,
            'latest_signal' => $latest_signal // أضفنا هذا السطر
        ]);
    }
}
