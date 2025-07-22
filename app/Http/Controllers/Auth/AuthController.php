<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Yeni rol sistemine göre yönlendirme
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isBusinessOwner()) {
                return redirect()->route('business.dashboard');
            } elseif ($user->isRestaurantManager()) {
                return redirect()->route('restaurant.dashboard');
            } elseif ($user->isCashier()) {
                return redirect()->route('restaurant.cashier');
            } elseif ($user->isWaiter()) {
                return redirect()->route('restaurant.waiter');
            } elseif ($user->isKitchen()) {
                return redirect()->route('restaurant.kitchen');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Girilen bilgiler hatalı.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // hashed cast otomatik hashler
            'role' => 'business_owner', // Varsayılan olarak işletme sahibi
        ]);

        Auth::login($user);

        return redirect()->route('business.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
