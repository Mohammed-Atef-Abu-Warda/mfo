<?php

namespace App\Services;

use App\Models\News;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Http;

class NewsAnalysisService
{
    public function fetchAndAnalyze()
    {
        // 1. جلب الأخبار الحية من Finnhub (أخبار السوق العام)
        $apiKey = env('FINNHUB_API_KEY');
        // زيادة الـ timeout وإضافة محاولات إعادة اتصال (retry)
        $response = Http::timeout(60) // زيادة وقت الانتظار لـ 60 ثانية
            ->retry(3, 100) // المحاولة 3 مرات في حال الفشل
            ->withoutVerifying()
            ->get("https://finnhub.io/api/v1/news", [
                'category' => 'general',
                'token' => 'd82qhppr01qvkevnhdggd82qhppr01qvkevnhdh0'
            ]);
        if (!$response->successful()) {
            return "فشل الاتصال بـ Finnhub";
        }

        // نأخذ آخر 5 أخبار فقط للتحليل حالياً (لتوفير رصيد OpenAI)
        $latestNews = collect($response->json())->take(5);

        foreach ($latestNews as $item) {
            // نستخدم headline كعنوان للخبر
            $title = $item['headline'];

            // التحقق من عدم تكرار الخبر
            if (News::where('title', $title)->exists()) {
                continue;
            }

            // 2. تحليل الخبر بالذكاء الاصطناعي
            $prompt = "أنت محلل مالي خبير. قم بتحليل هذا الخبر وتأثيره على سعر الذهب (XAU/USD): '{$title}'
            أعطني النتيجة بصيغة JSON:
            {
              'score': رقم بين -10 و 10,
              'reasoning': شرح قصير بالعربية
            }";

            $aiResponse = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You output valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $aiResult = json_decode($aiResponse->choices[0]->message->content, true);

            if ($aiResult) {
                // 3. حفظ الخبر الحقيقي في قاعدة البيانات
                News::create([
                    'title' => $title,
                    'source' => $item['source'] ?? 'Finnhub',
                    'ai_sentiment_score' => $aiResult['score'],
                    'ai_analysis' => $aiResult['reasoning'],
                    'published_at' => date('Y-m-d H:i:s', $item['datetime']),
                ]);
            }
        }

        return "تم جلب وتحليل الأخبار الحية بنجاح!";
    }

    public function getGlobalSentiment()
    {
        // جلب متوسط التقييمات لآخر 24 ساعة
        $average = News::where('created_at', '>=', now()->subDay())
                        ->avg('ai_sentiment_score');

        $average = round($average, 2);

        if ($average >= 3) {
            $signal = "شراء (Buy) 🟢";
        } elseif ($average <= -3) {
            $signal = "بيع (Sell) 🔴";
        } else {
            $signal = "انتظار (Wait/Neutral) 🟡";
        }

        return [
            'average' => $average,
            'signal' => $signal
        ];
    }

    
}