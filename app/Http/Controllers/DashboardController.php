<?php

namespace App\Http\Controllers;

use App\Services\PriceService;
use App\Models\Signal;
use App\Models\News;

class DashboardController extends Controller
{
    protected $priceService;

    public function __construct(PriceService $priceService)
    {
        $this->priceService = $priceService;
    }

    public function index()
    {
        // 1. جلب بيانات الذهب العالمي الجديد XAU/USD
        $priceData = $this->priceService->getLatestGoldData();

        // 2. جلب آخر إشارة تم رصدها من نظام القناص
        $latestSignal = Signal::where('pair', 'XAU/USD')
                              ->latest()
                              ->first();

        // 3. جلب الأخبار الاقتصادية المؤثرة على الذهب والدولار
        $newsData = News::latest()->take(5)->get();

        return view('dashboard', [
            'price' => $priceData,
            'latest_signal' => $latestSignal,
            'news' => $newsData
        ]);
    }
}