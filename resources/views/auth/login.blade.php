@extends('layouts.guest')

@section('title', 'Log In')

@section('content')
<div class="auth-form-container">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-arrow-right-to-bracket text-[#1a2a4a] text-xl"></i>
            Welcome back
        </h2>
        <p class="text-sm text-gray-500 mt-1.5">Log in to continue to your workspace.</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm space-y-1.5">
            @foreach($errors->all() as $error)
                <p class="flex items-start gap-2.5">
                    <i class="fas fa-exclamation-circle mt-0.5 text-red-500 text-xs"></i>
                    {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <!-- Session Status -->
    @if(session('status'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2.5">
            <i class="fas fa-check-circle text-green-500 text-sm"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Log In Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Email Address
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                </div>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       class="auth-input w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition @error('email') border-red-500 @enderror"
                       placeholder="you@company.com">
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required
                       class="auth-input w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition @error('password') border-red-500 @enderror"
                       placeholder="Enter your password">
                <button type="button" 
                        onclick="togglePasswordVisibility()" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-eye" id="passwordToggleIcon"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2.5 cursor-pointer group">
                <input id="remember" 
                       type="checkbox" 
                       name="remember"
                       class="w-4 h-4 text-[#1a2a4a] focus:ring-[#1a2a4a] border-gray-300 rounded transition">
                <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">Remember me</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#1a2a4a] hover:text-[#0f1a30] transition flex items-center gap-1.5">
                <i class="fas fa-key text-xs"></i>
                Forgot password?
            </a>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-btn w-full py-3.5 px-4 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition flex items-center justify-center gap-2 group">
            <i class="fas fa-sign-in-alt group-hover:translate-x-0.5 transition-transform"></i>
            Log In
            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>

    <!-- ============================================ -->
    <!-- LOG IN WITH GOOGLE                           -->
    <!-- ============================================ -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-xs">
            <span class="px-3 bg-white text-gray-400 font-medium">Or continue with</span>
        </div>
    </div>

    <a href="{{ route('google.redirect') }}"
       class="w-full inline-flex items-center justify-center gap-3 py-3 px-4 bg-white border border-gray-300 rounded-xl shadow-sm text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Log In with Google
    </a>

    <!-- Register Link -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-center text-sm text-gray-600">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-[#1a2a4a] hover:text-[#0f1a30] transition inline-flex items-center gap-1 group">
                Sign Up
                <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
            </a>
        </p>
    </div>
</div>

<!-- JavaScript for Password Toggle -->
<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<!-- Additional Styles -->
<style>
.auth-form-container {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.auth-input {
    transition: all 0.3s ease;
}

.auth-input:focus {
    border-color: #1a2a4a;
    box-shadow: 0 0 0 3px rgba(26, 42, 74, 0.1);
    outline: none;
}

.auth-input::placeholder {
    color: #9ca3af;
    font-size: 0.875rem;
}

.auth-btn {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    font-weight: 600;
    letter-spacing: 0.01em;
}

.auth-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
    transform: translateX(-100%);
    transition: transform 0.4s ease;
}

.auth-btn:hover::after {
    transform: translateX(0);
}

.auth-btn:active {
    transform: scale(0.98);
}

/* Checkbox styling */
input[type="checkbox"] {
    accent-color: #1a2a4a;
    cursor: pointer;
}

input[type="checkbox"]:focus {
    ring: 2px solid #1a2a4a;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .auth-form-container {
        padding: 0 0.5rem;
    }
    
    .auth-btn {
        font-size: 0.95rem;
    }
    
    .flex.items-center.justify-between {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
}
</style>
@endsection