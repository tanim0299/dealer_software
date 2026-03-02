@extends('driver.layouts.master')

@section('page_title', 'My Account')

@section('body')
@php
    $user = auth()->user();
    $userImage = !empty($user?->image) ? asset('storage' . $user->image) : null;
    $initials = collect(explode(' ', trim($user?->name ?? 'D')))
        ->filter()
        ->take(2)
        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
        ->join('');
@endphp

<div class="page-card p-3 mb-3">
    <div class="d-flex align-items-center gap-3">
        @if($userImage)
            <img src="{{ $userImage }}" alt="profile" style="width:58px;height:58px;border-radius:50%;object-fit:cover;border:2px solid #d9e7ff;">
        @else
            <div style="width:58px;height:58px;border-radius:50%;background:#e8f0ff;color:#0d6efd;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ $initials ?: 'DR' }}</div>
        @endif

        <div>
            <h6 class="mb-1 fw-bold">{{ $user->name }}</h6>
            <div class="text-muted small">{{ $user->email }}</div>
            <div class="text-muted small">{{ $user->phone }}</div>
        </div>
    </div>
</div>

<div class="page-card p-3 mb-3">
    <h6 class="mb-3 fw-bold">Change Password</h6>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-2">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" required>
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-2">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" required>
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password" required>
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-shield-lock me-1"></i> Update Password
        </button>
    </form>
</div>

<div class="page-card p-3">
    <h6 class="mb-3 fw-bold text-danger">Session</h6>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger w-100">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
    </form>
</div>
@endsection
