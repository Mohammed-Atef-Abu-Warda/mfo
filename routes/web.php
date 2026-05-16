<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
// use Illuminate\Support\Facades\Http;

use App\Http\Controllers\AuthController;

// مسارات تسجيل الدخول (متاحة للجميع)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// المسارات المحمية (يجب تسجيل الدخول لرؤيتها)
Route::middleware(['auth'])->group(function () {
    
    // صفحة رادار الذهب الرئيسية
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard'); 
    
    // مسارات إضافة الأشخاص (مفتوحة داخل الـ Middleware لكن الكنترولر يحميها للأدمن فقط)
    Route::get('/users/create', [AuthController::class, 'showCreateUser'])->name('users.create');
    Route::post('/users/create', [AuthController::class, 'storeUser'])->name('users.store');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';



// Route::get('/test-sniper-signal', function () {
//     // حل جذري: رفع قيود وقت التنفيذ إلى ما لا نهاية لهذا المسار فقط لمنع الـ Fatal Error
//     set_time_limit(0); 

//     $webhookUrl = env('DISCORD_WEBHOOK_URL');

//     if (!$webhookUrl) {
//         return "عذراً: لم يتم العثور على رابط DISCORD_WEBHOOK_URL في ملف .env";
//     }

//     // 1. إرسال إشارة الرادار اللحظية فوراً
//     Http::post($webhookUrl, [
//         'content' => "🚨 **[رادار القناص]:** تم رصد تباعد إيجابي (Bullish Divergence) على فريم الـ 5 دقائق! الـ RSI وصل لـ `20.86` والسعر يلامس قاع البولنجر باند. (سيتم تأكيد الصفقة بعد دقيقة واحدة إذا ثبتت السيولة) ⏳"
//     ]);

//     // الانتظار لمدة دقيقة كاملة (60 ثانية) بأمان الآن
//     sleep(60); 

//     // 2. إرسال تفاصيل الصفقة التجريبية بعد انتهاء الدقيقة
//     $orderPayload = [
//         "embeds" => [
//             [
//                 "title" => "🎯 صفقة قناص تجريبية المباشرة (PAXG/USDT)",
//                 "description" => "تم تأكيد ثبات السيولة بعد دقيقة من المراقبة والتقاط نقطة الارتداد بنجاح.",
//                 "color" => 3066993, 
//                 "fields" => [
//                     [
//                         "name" => "نوع الصفقة",
//                         "value" => "🟢 **شراء فوري (BUY)**",
//                         "inline" => true
//                     ],
//                     [
//                         "name" => "سعر الدخول الحالي",
//                         "value" => "`$4,535.00`",
//                         "inline" => true
//                     ],
//                     [
//                         "name" => "وقف الخسارة (SL)",
//                         "value" => "🔴 `$4,510.00`",
//                         "inline" => false
//                     ],
//                     [
//                         "name" => "الهدف الأول (TP1)",
//                         "value" => "🎯 `$4,560.00`",
//                         "inline" => true
//                     ],
//                     [
//                         "name" => "الهدف الثاني (TP2)",
//                         "value" => "🎯 `$4,590.00`",
//                         "inline" => true
//                     ]
//                 ],
//                 "footer" => [
//                     "text" => "نظام القناص الذكي v2.5 • تم التأكيد بعد دقيقة مراقبة"
//                 ],
//                 "timestamp" => now()->toISOString()
//             ]
//         ]
//     ];

//     Http::post($webhookUrl, $orderPayload);

//     return "🚀 نجحت التجربة! تم إرسال الإشارة أولاً، وانتظر السيرفر 60 ثانية كاملة، ثم أرسل الصفقة بنجاح دون انهيار.";
// });