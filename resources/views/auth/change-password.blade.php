@extends('layouts.app')
@section('title', 'Change Password - OAT')
@section('page-title', 'Change Password')

@section('content')
<div class="max-w-lg mx-auto fade-in">
    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center">
                <i class="fas fa-key text-primary-500"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Change Password</h3>
                <p class="text-xs text-slate-500">Update your account password</p>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.change') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Current Password</label>
                <input type="password" name="current_password" required
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">New Password</label>
                <input type="password" name="password" required minlength="6"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Confirm New Password</label>
                <input type="password" name="password_confirmation" required minlength="6"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors shadow-sm shadow-primary-200">
                <i class="fas fa-save mr-1"></i> Update Password
            </button>
        </form>
    </div>
</div>
@endsection
