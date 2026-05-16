<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول - رادار الذهب</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-950 text-white flex items-center justify-center h-screen">
    <div class="bg-gray-900 p-8 rounded-2xl shadow-2xl border border-gray-800 w-full max-w-md">
        <h2 class="text-2xl font-black text-center mb-6 text-yellow-500">📊 تسجيل دخول رادار الذهب</h2>
        
        @if($errors->any())
            <div class="bg-red-900 text-red-200 p-3 rounded mb-4 text-xs font-bold">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-1">البريد الإلكتروني</label>
                <input type="email" name="email" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2.5 text-white focus:outline-none focus:border-yellow-500" required>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">كلمة المرور</label>
                <input type="password" name="password" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2.5 text-white focus:outline-none focus:border-yellow-500" required>
            </div>
            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-gray-950 font-black p-3 rounded-lg transition-colors mt-6">دخول النظام 🚀</button>
        </form>
    </div>
</body>
</html>