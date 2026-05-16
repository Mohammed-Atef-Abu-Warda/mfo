<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة مستخدم جديد</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-950 text-white flex items-center justify-center h-screen">
    <div class="bg-gray-900 p-8 rounded-2xl shadow-2xl border border-gray-800 w-full max-w-md">
        <h2 class="text-2xl font-black text-center mb-6 text-blue-400">➕ إضافة شخص جديد للمنظومة</h2>
        
        @if($errors->any())
            <div class="bg-red-900 text-red-200 p-3 rounded mb-4 text-xs">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-1">اسم الشخص</label>
                <input type="text" name="name" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2.5" required>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">البريد الإلكتروني (الدخول)</label>
                <input type="email" name="email" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2.5" required>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">كلمة المرور البدئية</label>
                <input type="password" name="password" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2.5" required>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">الصلاحية</label>
                <select name="role" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2.5 text-white">
                    <option value="user">مستخدم عادي (مشاهدة الرادار فقط)</option>
                    <option value="admin">أدمن (يستطيع إضافة أشخاص آخرين)</option>
                </select>
            </div>
            <div class="flex gap-4 mt-6">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 font-bold p-3 rounded-lg transition-colors">تأكيد الإضافة وعمل الحساب</button>
                <a href="{{ route('dashboard') }}" class="bg-gray-700 hover:bg-gray-600 font-bold p-3 rounded-lg text-center text-sm flex items-center justify-center">إلغاء</a>
            </div>
        </form>
    </div>
</body>
</html>