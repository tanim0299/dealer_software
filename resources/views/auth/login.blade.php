<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — {{ $settings->title ?? 'Fresh Foods' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --login-accent: #0d9488;
            --login-accent-dark: #0f766e;
            --login-text: #0f172a;
            --login-muted: #64748b;
            --login-border: #e2e8f0;
            --login-radius: 20px;
            --login-shadow: 0 4px 6px rgba(15, 23, 42, 0.04), 0 24px 48px rgba(15, 23, 42, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            font-family: "Plus Jakarta Sans", system-ui, sans-serif;
            color: var(--login-text);
            background:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(13, 148, 136, 0.18), transparent),
                radial-gradient(ellipse 60% 50% at 100% 100%, rgba(99, 102, 241, 0.08), transparent),
                linear-gradient(165deg, #f0fdfa 0%, #f1f5f9 45%, #e0f2fe 100%);
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: var(--login-radius);
            box-shadow: var(--login-shadow);
            padding: 2.5rem 2rem 2rem;
        }

        .login-logo {
            display: block;
            margin: 0 auto 2rem;
            max-height: 72px;
            max-width: 200px;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .login-logo-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            margin: 0 auto 2rem;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--login-accent), var(--login-accent-dark));
            color: #fff;
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .login-alert {
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .login-alert ul {
            margin: 0;
            padding-left: 1.1rem;
        }

        .login-field {
            margin-bottom: 1.15rem;
        }

        .login-field label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--login-muted);
            margin-bottom: 0.4rem;
        }

        .login-field input {
            width: 100%;
            padding: 0.85rem 1rem;
            font-size: 1rem;
            font-family: inherit;
            color: var(--login-text);
            background: #f8fafc;
            border: 1.5px solid var(--login-border);
            border-radius: 12px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .login-field input::placeholder {
            color: #94a3b8;
        }

        .login-field input:hover {
            border-color: #cbd5e1;
        }

        .login-field input:focus {
            outline: none;
            background: #fff;
            border-color: var(--login-accent);
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.15);
        }

        .login-field input.is-invalid {
            border-color: #f87171;
            background: #fffafb;
        }

        .login-submit {
            width: 100%;
            margin-top: 0.5rem;
            padding: 0.9rem 1.25rem;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            color: #fff;
            background: linear-gradient(135deg, var(--login-accent) 0%, var(--login-accent-dark) 100%);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .login-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(13, 148, 136, 0.4);
        }

        .login-submit:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="login-card">
        @php
            $logoUrl = ! empty($settings->logo) ? asset('storage/' . ltrim($settings->logo, '/')) : null;
            $brandInitial = strtoupper(substr($settings->title ?? 'F', 0, 1));
        @endphp

        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $settings->title ?? 'Logo' }}" class="login-logo">
        @else
            <div class="login-logo-fallback" aria-hidden="true">{{ $brandInitial }}</div>
        @endif

        @if (session('error') || $errors->any())
            <div class="login-alert" role="alert">
                @if (session('error'))
                    {{ session('error') }}
                @else
                    <ul>
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="login-field">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    required
                    autofocus
                    autocomplete="username"
                    class="@error('email') is-invalid @enderror">
            </div>

            <div class="login-field">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                    class="@error('password') is-invalid @enderror">
            </div>

            <button type="submit" class="login-submit">Sign in</button>
        </form>
    </div>
</body>
</html>
