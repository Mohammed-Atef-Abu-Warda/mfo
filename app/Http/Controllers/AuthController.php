<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // عرض صفحة تسجيل الدخول
    public function showLogin() {
        return view('auth.login');
    }

    // معالجة تسجيل الدخول
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'البيانات المدخلة غير مطابقة لسجلاتنا.']);
    }

    // تسجيل الخروج
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // عرض صفحة إضافة شخص جديد (للأدمن فقط)
    public function showCreateUser() {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'غير مسموح لك بالوصول لهذه الصفحة');
        }
        return view('auth.create-user');
    }

    // حفظ الشخص الجديد في قاعدة البيانات
    public function storeUser(Request $request) {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('dashboard')->with('success', 'تم إضافة المستخدم بنجاح!');
    }
}