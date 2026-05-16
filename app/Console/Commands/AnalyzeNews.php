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
    protected $description = 'نظام القناص المتقدم: تحليل إخباري + فريمات متعددة (1H, 15M, 5M) + بولنجر باند (Discord Only)';

    /**
     * تنفيذ الأمر البرمجي
     */
    public function handle(
        NewsAnalysisService $newsService, 
        PriceService $priceService, 
        DiscordService $discordService
    ) {
        $this->info('🚀 جاري تشغيل نظام القناص (Triple Screen + Discord Mode)...');

        // 1. تحليل الأخبار (معالجة أخطاء التوقف)
        try {
            $this->info('🌐 جاري فحص وتحليل الأخبار العالمية عبر AI...');
            $newsService->fetchAndAnalyze();
            $avgScore = News::where('created_at', '>=', now()->subHours(24))->avg('ai_sentiment_score') ?? 0;
        } catch (\Exception $e) {
            $this->error('⚠️ تنبيه: فشل تحديث الأخبار.');
            $avgScore = 0; 
        }

        // 2. جلب التحليل الفني المتقدم (يشمل الآن فريم 5 دقائق)
        $techData = $priceService->getAdvancedAnalysis();
        
        if (!$techData) {
            $this->error('❌ فشل جلب البيانات من Binance.');
            return;
        }

        $decision = 'WAIT';
        $strength = 'Neutral';

        // 3. منطق "القناص" الاحترافي (الاتفاق الثلاثي للفريمات)
        if (!$techData['is_market_dead']) {
            // شروط الشراء: خبر إيجابي + ترند صاعد على (ساعة و 15 دقيقة و 5 دقائق) + ارتداد من قاع البولنجر 5M
            if ($avgScore > 2 && $techData['hourly_trend'] == 'UP' && $techData['m15_trend'] == 'UP' && $techData['m5_trend'] == 'UP') {
                if ($techData['price'] <= $techData['bb_lower'] * 1.0005) { 
                    $decision = 'BUY';
                    $strength = "قناص: توافق ثلاثي صاعد مع ارتداد قاع البولنجر (5M)";
                }
            } 
            
            // شروط البيع: خبر سلبي + ترند هابط على (ساعة و 15 دقيقة و 5 دقائق) + ارتداد من سقف البولنجر 5M
            elseif ($avgScore < -2 && $techData['hourly_trend'] == 'DOWN' && $techData['m15_trend'] == 'DOWN' && $techData['m5_trend'] == 'DOWN') {
                if ($techData['price'] >= $techData['bb_upper'] * 0.9995) {
                    $decision = 'SELL';
                    $strength = "قناص: توافق ثلاثي هابط مع ارتداد سقف البولنجر (5M)";
                }
            }
        } else {
            $this->warn("😴 السوق ميت (التقلب ضعيف جداً على فريم الـ 5 دقائق)، لن نغامر بالدخول.");
        }

        // 4. نظام الإنذار المبكر (ديسكورد فقط)
        if ($decision == 'WAIT') {
            $isPreBuy = ($avgScore > 1.5 && $techData['rsi'] < 35);
            $isPreSell = ($avgScore < -1.5 && $techData['rsi'] > 65);

            if ($isPreBuy || $isPreSell) {
                $type = $isPreBuy ? 'BUY' : 'SELL';
                $msg = "🚨 رادار الذهب: يقترب من منطقة دخول $type مدعومة بالـ RSI الحالي ({$techData['rsi']}) على فريم الـ 5 دقائق!";
                
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

                // إرسال الإشعار لديسكورد
                $discordService->sendSignal($decision, $techData['price'], round($avgScore, 2), $strength);

                $this->info("✅ نجاح: تم تسجيل وإرسال الإشارة إلى ديسكورد.");
            } else {
                $this->warn("⏳ إشارة $decision مكررة، تم الاكتفاء بالتسجيل الصامت منعاً للإزعاج.");
            }
        } else {
            $this->info('🟡 حالة السوق: مراقبة صامتة بانتظار توافق الفرصة الحقيقية.');
        }

        // 6. عرض لوحة التحكم بالتيرمنال (شاملة فريم 5 دقائق الجديد)
        $this->newLine();
        $this->table(
            ['القرار', 'السعر اللحظي', 'قوة الخبر', 'RSI (5M)', 'H1 Trend', 'M15 Trend', 'M5 Trend'],
            [[
                $decision, 
                "$" . number_format($techData['price'], 2), 
                round($avgScore, 2), 
                $techData['rsi'], 
                $techData['hourly_trend'], 
                $techData['m15_trend'],
                $techData['m5_trend']
            ]]
        );
        $this->newLine();
    }
}