<!doctype html>
<html lang="bn">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings->title }} Login</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0d6efd10, #0dcaf010);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            max-width: 420px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(13, 110, 253, 0.08);
            overflow: hidden;
        }

        .brand {
            background: linear-gradient(90deg, #0d6efd, #6610f2);
            color: #fff;
            padding: 18px;
            text-align: center;
        }

        .brand h4 {
            margin: 0;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .form-section {
            padding: 22px;
        }

        .help-text {
            font-size: .9rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="auth-card bg-white">
        <div class="brand">
            <img src="{{ asset('storage/') }}{{ !empty($settings->logo) ? $settings->logo : '' }}" alt=""
                style="width: 50%;padding: 12px;"><br>
            <h4>{{ $settings->title }}</h4>
            <div class="small">Login to your account</div>
        </div>

        <div class="form-section">
            <!-- session error (for custom auth failure) -->
            @if (session('error'))
                <div class="alert alert-danger alert-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- validation errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email / Username</label>
                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" placeholder="Your password" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    <div>
                        <a href="{{ route('password.request') }}" class="small">Forgot?</a>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                </div>

                <div class="text-center help-text">
                    Don't have an account? <a href="{{ route('register') }}">Register</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
