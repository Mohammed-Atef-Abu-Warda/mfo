<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رادار الذهب الذكي - نظام القناص</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="60">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>

<body class="bg-gray-900 text-white p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8 border-b border-gray-700 pb-4">
            <h1 class="text-3xl font-black">📊 رادار الذهب الذكي <span class="text-sm font-normal text-gray-500 mr-2">نسخة القناص v2.0</span></h1>
            <div class="text-left text-xs text-gray-400">تحديث تلقائي كل دقيقة</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-l-4 {{ ($price['hourly_trend'] ?? 'UP') == 'UP' ? 'border-green-500' : 'border-red-500' }}">
                <p class="text-gray-400">سعر PAXG المباشر</p>
                <h2 class="text-4xl font-black">${{ number_format($price['price'] ?? 0, 2) }}</h2>
                <p class="mt-2 font-bold {{ ($price['hourly_trend'] ?? 'UP') == 'UP' ? 'text-green-400' : 'text-red-400' }}">
                    الترند (1H): {{ ($price['hourly_trend'] ?? 'UP') == 'UP' ? 'صاعد 📈' : 'هابط 📉' }}
                </p>
            </div>

            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                <p class="text-gray-400">مؤشر القوة النسبية (RSI)</p>
                <h2 class="text-4xl font-black {{ ($price['rsi'] ?? 50) > 70 ? 'text-red-500' : (($price['rsi'] ?? 50) < 30 ? 'text-green-500' : 'text-blue-400') }}">
                    {{ $price['rsi'] ?? '50.0' }}
                </h2>
                <p class="mt-2 text-sm {{ ($price['m15_trend'] ?? 'UP') == 'UP' ? 'text-green-400' : 'text-red-400' }}">
                    زخم (15m): {{ ($price['m15_trend'] ?? 'UP') == 'UP' ? 'قوي' : 'ضعيف' }}
                </p>
            </div>

            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-l-4 border-yellow-500">
                <p class="text-gray-400">نطاق البولنجر (BB)</p>
                <div class="text-sm mt-1 text-gray-300">
                    <div>سقف: <span class="text-red-400 font-bold">${{ $price['bb_upper'] ?? '0' }}</span></div>
                    <div>قاع: <span class="text-green-400 font-bold">${{ $price['bb_lower'] ?? '0' }}</span></div>
                </div>
                @if($price['is_market_dead'] ?? false)
                    <p class="text-xs text-yellow-500 font-bold mt-2 animate-pulse">⚠️ تحذير: السيولة منخفضة جداً</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-800 p-6 rounded-xl shadow-lg flex flex-col items-center justify-center border-t-4 border-purple-500">
                <p class="text-gray-400 mb-2">إشارة الدخول الحالية</p>
                @php $type = $latest_signal->type ?? 'WAIT'; @endphp
                
                @if($type == 'BUY')
                    <div class="w-16 h-16 bg-green-500 rounded-full animate-pulse shadow-[0_0_30px_rgba(34,197,94,0.6)] flex items-center justify-center">
                        <span class="text-2xl">🚀</span>
                    </div>
                    <h2 class="text-3xl font-black text-green-400 mt-4 text-center">شراء الآن (BUY)</h2>
                @elseif($type == 'SELL')
                    <div class="w-16 h-16 bg-red-500 rounded-full animate-pulse shadow-[0_0_30px_rgba(239,68,68,0.6)] flex items-center justify-center">
                        <span class="text-2xl">📉</span>
                    </div>
                    <h2 class="text-3xl font-black text-red-400 mt-4 text-center">بيع الآن (SELL)</h2>
                @else
                    <div class="w-16 h-16 bg-gray-600 rounded-full shadow-inner flex items-center justify-center">
                        <span class="text-2xl">⏳</span>
                    </div>
                    <h2 class="text-3xl font-black text-gray-400 mt-4 text-center">انتظار الفرصة</h2>
                @endif
            </div>

            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-blue-600">
                <p class="text-gray-400">تحليل معنويات السوق (AI Score)</p>
                @php $score = $latest_signal->sentiment_score ?? 0; @endphp
                <h2 class="text-5xl font-black mt-2 {{ $score > 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ number_format($score, 1) }}
                </h2>
                <p class="text-gray-500 mt-2 text-sm italic">متوسط قوة الخبر آخر 24 ساعة</p>
                <div class="w-full bg-gray-700 h-3 mt-6 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full transition-all duration-1000" style="width: {{ min(max(($score + 10) * 5, 0), 100) }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-gray-800 rounded-xl overflow-hidden shadow-xl border border-gray-700">
                <div class="bg-gray-700 p-4 font-bold text-yellow-500 flex justify-between">
                    <span>📋 سجل الإشارات (القناص)</span>
                    <span class="text-xs text-gray-400">آخر 10 صفقات</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-900/50 text-gray-400">
                            <tr class="border-b border-gray-700">
                                <th class="p-4">الوقت</th>
                                <th class="p-4">النوع</th>
                                <th class="p-4">السعر</th>
                                <th class="p-4">القوة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\Signal::latest()->take(10)->get() as $sig)
                            <tr class="border-b border-gray-700 hover:bg-gray-750 transition-colors">
                                <td class="p-4 text-gray-400">{{ $sig->created_at->diffForHumans() }}</td>
                                <td class="p-4 font-bold {{ $sig->type == 'BUY' ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $sig->type }}
                                </td>
                                <td class="p-4 font-mono font-bold">${{ number_format($sig->price_at_signal, 2) }}</td>
                                <td class="p-4 text-xs text-gray-500">{{ $sig->strength }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-8 text-center text-gray-600 italic">بانتظار التقاء الشروط الفنية...</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-gray-800 rounded-xl overflow-hidden shadow-xl border border-gray-700">
                <div class="bg-gray-700 p-4 font-bold text-blue-400">🗞️ آخر أخبار الذهب المكتشفة</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-900/50 text-gray-400">
                            <tr>
                                <th class="p-4">الخبر</th>
                                <th class="p-4">تأثير AI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($news as $item)
                            <tr class="border-b border-gray-700">
                                <td class="p-4 leading-relaxed">{{ $item->title }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 rounded text-xs font-bold {{ $item->ai_sentiment_score > 0 ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                                        {{ $item->ai_sentiment_score > 0 ? '+' : '' }}{{ $item->ai_sentiment_score }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <audio id="alert-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <script>
        // تخزين الحالة السابقة في "ذاكرة المتصفح المحلية" لكي لا يضيع عند التحديث
        let lastKnownSignal = localStorage.getItem('last_signal_type') || "WAIT";
        let currentSignal = "{{ $latest_signal->type ?? 'WAIT' }}";

        window.onload = function() {
            // إذا تغيرت الحالة الآن إلى شراء أو بيع وكانت سابقاً انتظار
            if (currentSignal !== "WAIT" && currentSignal !== lastKnownSignal) {
                playAlert();
                // عرض تنبيه في المتصفح أيضاً
                if (Notification.permission === "granted") {
                    new Notification("إشارة تداول جديدة: " + currentSignal);
                }
            }
            // حفظ الحالة الحالية للدورة القادمة
            localStorage.setItem('last_signal_type', currentSignal);
        };

        function playAlert() {
            const sound = document.getElementById('alert-sound');
            sound.play().catch(e => {
                console.log("يرجى النقر على أي مكان في الصفحة لتفعيل التنبيهات الصوتية");
            });
        }

        // طلب إذن التنبيهات من المستخدم عند أول دخول
        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }
    </script>