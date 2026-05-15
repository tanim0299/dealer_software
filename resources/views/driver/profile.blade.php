@extends('driver.layouts.master')

@section('page_title', 'Account')

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

    <div class="page-card p-4 mb-3 text-center">
        @if($userImage)
            <img src="{{ $userImage }}" alt="" class="rounded-circle mb-3 border shadow-sm" style="width:88px;height:88px;object-fit:cover;">
        @else
            <div class="driver-avatar-fallback d-block mx-auto mb-3" style="width:88px;height:88px;font-size:1.5rem;">{{ $initials ?: 'D' }}</div>
        @endif
        <h2 class="h5 mb-1 fw-bold">{{ $user->name }}</h2>
        <p class="small text-muted mb-0">{{ $user->email }}</p>
        @if($user->phone)
            <p class="small text-muted">{{ $user->phone }}</p>
        @endif
    </div>

    <div class="driver-section-title">Security</div>
    <div class="page-card p-3 mb-3">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Current password</label>
                <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" required>
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">New password</label>
                <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" required>
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm password</label>
                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password" required>
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-shield-lock me-1"></i> Update password
            </button>
        </form>
    </div>

    <div class="page-card p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
@endsection
