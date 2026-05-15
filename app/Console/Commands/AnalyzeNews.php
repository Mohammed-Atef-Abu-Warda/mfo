<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAnalysisService;
use App\Services\PriceService;
use App\Services\DiscordService;
use App\Models\Signal;
use App\Models\News;

class AnalyzeNews extends Command
{
    // اسم الأمر البرمجي
    protected $signature = 'news:analyze';
    
    // وصف النظام
    protected $description = 'نظام القناص المتقدم: تحليل إخباري + فريمات متعددة + بولنجر باند (Discord Only)';

    /**
     * تنفيذ الأمر البرمجي
     */
    public function handle(
        NewsAnalysisService $newsService, 
        PriceService $priceService, 
        DiscordService $discordService
    ) {
        $this->info('🚀 جاري تشغيل نظام القناص (Discord Mode)...');

        // 1. تحليل الأخبار (معالجة أخطاء التوقف)
        try {
            $this->info('🌐 جاري فحص وتحليل الأخبار العالمية عبر AI...');
            $newsService->fetchAndAnalyze();
            $avgScore = News::where('created_at', '>=', now()->subHours(24))->avg('ai_sentiment_score') ?? 0;
        } catch (\Exception $e) {
            $this->error('⚠️ تنبيه: فشل تحديث الأخبار.');
            $avgScore = 0; 
        }

        // 2. جلب التحليل الفني المتقدم
        $techData = $priceService->getAdvancedAnalysis();
        
        if (!$techData) {
            $this->error('❌ فشل جلب البيانات من Binance.');
            return;
        }

        $decision = 'WAIT';
        $strength = 'Neutral';

        // 3. منطق "القناص"
        if (!$techData['is_market_dead']) {
            // شروط الشراء
            if ($avgScore > 2 && $techData['hourly_trend'] == 'UP' && $techData['m15_trend'] == 'UP') {
                if ($techData['price'] <= $techData['bb_lower'] * 1.0005) { 
                    $decision = 'BUY';
                    $strength = "قناص: ارتداد من القاع الفني مع زخم إيجابي";
                }
            } 
            
            // شروط البيع
            elseif ($avgScore < -2 && $techData['hourly_trend'] == 'DOWN' && $techData['m15_trend'] == 'DOWN') {
                if ($techData['price'] >= $techData['bb_upper'] * 0.9995) {
                    $decision = 'SELL';
                    $strength = "قناص: ارتداد من السقف الفني مع زخم سلبي";
                }
            }
        }

        // 4. نظام الإنذار المبكر (ديسكورد فقط)
        if ($decision == 'WAIT') {
            $isPreBuy = ($avgScore > 1.5 && $techData['rsi'] < 35);
            $isPreSell = ($avgScore < -1.5 && $techData['rsi'] > 65);

            if ($isPreBuy || $isPreSell) {
                $type = $isPreBuy ? 'BUY' : 'SELL';
                $msg = "الذهب يقترب من منطقة دخول $type قوية! (RSI: {$techData['rsi']})";
                
                $this->warn("🔔 إنذار مبكر: $msg");
                $discordService->sendAlert($msg);
            }
        }

        // 5. إدارة التنبيهات ومنع التكرار
        if ($decision !== 'WAIT') {
            $lastSignal = Signal::latest()->first();
            
            // منع إرسال نفس الإشارة إذا صدرت خلال آخر 20 دقيقة
            $isDuplicate = $lastSignal && 
                           $lastSignal->type == $decision && 
                           $lastSignal->created_at->diffInMinutes(now()) < 20;

            if (!$isDuplicate) {
                Signal::create([
                    'type' => $decision,
                    'price_at_signal' => $techData['price'],
                    'sentiment_score' => $avgScore,
                    'strength' => $strength
                ]);

                // إرسال الإشعار لديسكورد فقط
                $discordService->sendSignal($decision, $techData['price'], round($avgScore, 2), $strength);

                $this->info("✅ نجاح: تم إرسال الإشارة إلى ديسكورد.");
            } else {
                $this->warn("⏳ إشارة $decision مكررة، تم الاكتفاء بالتسجيل الصامت.");
            }
        } else {
            $this->info('🟡 حالة السوق: مراقبة صامتة.');
        }

        // 6. عرض لوحة التحكم
        $this->newLine();
        $this->table(
            ['القرار', 'السعر', 'قوة الخبر', 'RSI', 'ترند 1H', 'ترند 15M'],
            [[
                $decision, 
                "$" . number_format($techData['price'], 2), 
                round($avgScore, 2), 
                $techData['rsi'], 
                $techData['hourly_trend'], 
                $techData['m15_trend']
            ]]
        );
        $this->newLine();
    }
}