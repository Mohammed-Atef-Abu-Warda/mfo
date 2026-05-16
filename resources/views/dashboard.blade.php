<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رادار الذهب الذكي - نظام القناص (XAU/USD)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="60">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>

<body class="bg-gray-900 text-white p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        
        <div class="bg-gray-800 p-4 mb-6 rounded-xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-lg border border-gray-700">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm bg-gray-700 px-3 py-1.5 rounded-lg text-yellow-400 font-bold flex items-center gap-1">
                    👤 {{ Auth::user()->name }} 
                    <span class="text-xs text-gray-400">({{ Auth::user()->role == 'admin' ? 'أدمن' : 'مستخدم' }})</span>
                </span>
                
                @if(Auth::user()->role == 'admin')
                    <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition-colors shadow-md">
                        ➕ إضافة شخص جديد للدخول
                    </a>
                @endif
            </div>
            
            <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto text-left">
                @csrf
                <button type="submit" class="text-xs text-red-400 hover:text-red-500 font-bold border border-red-950 hover:border-red-500/30 px-3 py-1.5 rounded-lg transition-all">
                    🚪 تسجيل الخروج
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-900 text-green-200 p-3 rounded-lg mb-6 text-center text-sm font-bold border border-green-700 animate-bounce">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="flex justify-between items-center mb-8 border-b border-gray-700 pb-4">
            <h1 class="text-3xl font-black">📊 رادار الذهب الفوري <span class="text-sm font-normal text-yellow-500 mr-2">XAU/USD • نسخة القناص v2.5</span></h1>
            <div class="text-left text-xs text-gray-400">تحديث تلقائي كل دقيقة</div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-l-4 {{ ($price['hourly_trend'] ?? 'UP') == 'UP' ? 'border-green-500' : 'border-red-500' }}">
                <p class="text-gray-400">سعر أونصة الذهب المباشر</p>
                <h2 class="text-3xl font-black">${{ number_format($price['price'] ?? 0, 2) }}</h2>
                <p class="mt-2 font-bold text-sm {{ ($price['hourly_trend'] ?? 'UP') == 'UP' ? 'text-green-400' : 'text-red-400' }}">
                    الاتجاه (1H): {{ ($price['hourly_trend'] ?? 'UP') == 'UP' ? 'صاعد 📈' : 'هابط 📉' }}
                </p>
            </div>

            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-l-4 {{ ($price['m15_trend'] ?? 'UP') == 'UP' ? 'border-green-500' : 'border-red-500' }}">
                <p class="text-gray-400">زخم السوق (15M)</p>
                <h2 class="text-3xl font-black {{ ($price['m15_trend'] ?? 'UP') == 'UP' ? 'text-green-400' : 'text-red-400' }}">
                    {{ ($price['m15_trend'] ?? 'UP') == 'UP' ? 'قوة صاعدة' : 'ضغط بيع' }}
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    الاتجاه (15m): {{ ($price['m15_trend'] ?? 'UP') == 'UP' ? 'صاعد 📈' : 'هابط 📉' }}
                </p>
            </div>

            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-l-4 {{ ($price['m5_trend'] ?? 'UP') == 'UP' ? 'border-green-500' : 'border-red-500' }}">
                <p class="text-gray-400">التأكيد اللحظي (5M)</p>
                <h2 class="text-3xl font-black {{ ($price['m5_trend'] ?? 'UP') == 'UP' ? 'text-green-400' : 'text-red-400' }}">
                    {{ ($price['m5_trend'] ?? 'UP') == 'UP' ? 'دخول سيولة' : 'خروج سيولة' }}
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    الاتجاه (5m): {{ ($price['m5_trend'] ?? 'UP') == 'UP' ? 'صاعد 📈' : 'هابط 📉' }}
                </p>
            </div>

            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-400">مؤشر RSI (5M)</p>
                        <h2 class="text-2xl font-black mt-1 {{ ($price['rsi'] ?? 50) > 70 ? 'text-red-500' : (($price['rsi'] ?? 50) < 30 ? 'text-green-500' : 'text-blue-400') }}">
                            {{ $price['rsi'] ?? '50.0' }}
                        </h2>
                    </div>
                    <div class="text-left text-xs text-gray-400 border-r border-gray-700 pr-2">
                        <div>سقف: <span class="text-red-400 font-bold">${{ $price['bb_upper'] ?? '0' }}</span></div>
                        <div>قاع: <span class="text-green-400 font-bold">${{ $price['bb_lower'] ?? '0' }}</span></div>
                    </div>
                </div>
                @if($price['is_market_dead'] ?? false)
                    <p class="text-xs text-yellow-500 font-bold mt-2 animate-pulse">⚠️ تنبيه: ضعف في أحجام التداول (سيولة ضعيفة)</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-800 p-6 rounded-xl shadow-lg flex flex-col items-center justify-center border-t-4 border-purple-500">
                <p class="text-gray-400 mb-2">إشارة دخول XAU/USD الحالية</p>
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
                    <h2 class="text-3xl font-black text-gray-400 mt-4 text-center">انتظار شروط القناص</h2>
                @endif
            </div>

            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-blue-600">
                <p class="text-gray-400">تحليل معنويات أخبار الذهب الفوري (AI Score)</p>
                @php $score = $latest_signal->sentiment_score ?? 0; @endphp
                <h2 class="text-5xl font-black mt-2 {{ $score > 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ number_format($score, 1) }}
                </h2>
                <p class="text-gray-500 mt-2 text-sm italic">مؤشر الأخبار المؤثرة على الدولار وعوائد السندات</p>
                <div class="w-full bg-gray-700 h-3 mt-6 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full transition-all duration-1000" style="width: {{ min(max(($score + 10) * 5, 0), 100) }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-gray-800 rounded-xl overflow-hidden shadow-xl border border-gray-700">
                <div class="bg-gray-700 p-4 font-bold text-yellow-500 flex justify-between">
                    <span>📋 سجل صفقات XAU/USD (القناص)</span>
                    <span class="text-xs text-gray-400">آخر 10 صفقات فوركس</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-900/50 text-gray-400">
                            <tr class="border-b border-gray-700">
                                <th class="p-4">الوقت</th>
                                <th class="p-4">النوع</th>
                                <th class="p-4">سعر الأونصة</th>
                                <th class="p-4">الاستراتيجية التوافقية</th>
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
                                <td class="p-4 text-xs text-gray-400">{{ $sig->strength }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-8 text-center text-gray-600 italic">بانتظار التقاء الفريمات الثلاثة لـ XAU/USD...</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-gray-800 rounded-xl overflow-hidden shadow-xl border border-gray-700">
                <div class="bg-gray-700 p-4 font-bold text-blue-400">🗞️ التحليل الإخباري الفوري لأسواق الذهب العالمية</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-900/50 text-gray-400">
                            <tr>
                                <th class="p-4">الأخبار الفيدرالية والاقتصادية المؤثرة</th>
                                <th class="p-4">وزن التأثير</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($news as $item)
                            <tr class="border-b border-gray-700">
                                <td class="p-4 leading-relaxed text-xs">{{ $item->title }}</td>
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
        let lastKnownSignal = localStorage.getItem('last_signal_type') || "WAIT";
        let currentSignal = "{{ $latest_signal->type ?? 'WAIT' }}";

        window.onload = function() {
            if (currentSignal !== "WAIT" && currentSignal !== lastKnownSignal) {
                playAlert();
                if (Notification.permission === "granted") {
                    new Notification("إشارة XAU/USD جديدة: " + currentSignal, {
                        body: "نظام القناص رصد تطابق كامل على فريمات الذهب الفوري الآن."
                    });
                }
            }
            localStorage.setItem('last_signal_type', currentSignal);
        };

        function playAlert() {
            const sound = document.getElementById('alert-sound');
            sound.play().catch(e => {
                console.log("تفاعل مع الصفحة لتفعيل الصوت.");
            });
        }

        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }
    </script>
</body>
</html>