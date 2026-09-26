@extends('layouts.guest')

@section('title', 'Confirm Password')

@section('content')
<div class="mb-7">
    <a href="/" class="text-2xl font-extrabold text-gray-900">
        Equal<span class="text-[#1a2a4a]">Flow</span>
    </a>
</div>

<h2 class="text-2xl font-bold text-gray-900">Confirm Password</h2>
<p class="text-sm text-gray-500 mt-1 mb-7">
    Please confirm your password before continuing.
</p>

@if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
    @csrf

    <div>
        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
        <input id="password" type="password" name="password" required
               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition @error('password') border-red-500 @enderror"
               placeholder="Enter your password">
    </div>

    <button type="submit" class="w-full py-3 px-4 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition">
        Confirm Password
    </button>
</form>

<p class="mt-6 text-center text-sm text-gray-600">
    <a href="{{ route('logout') }}" class="font-medium text-[#1a2a4a] hover:text-[#0f1a30]"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        Logout
    </a>
</p>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>
@endsection