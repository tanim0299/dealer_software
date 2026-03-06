<!doctype html>
<html lang="bn">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings->title }} Login</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e8ba3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Animated background elements */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            left: -100px;
            top: -100px;
            animation: float 6s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            right: -50px;
            bottom: -50px;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(20px); }
        }

        .login-container {
            display: flex;
            gap: 0;
            max-width: 1100px;
            width: 95%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }

        .login-left {
            background: linear-gradient(135deg, #2c5f7c 0%, #1e3a52 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 350px;
            text-align: center;
        }

        .login-left h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #fff;
        }

        .login-left p {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .inventory-features {
            list-style: none;
            margin-top: 20px;
            text-align: left;
        }

        .inventory-features li {
            margin: 12px 0;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .inventory-features li i {
            color: #4CAF50;
            margin-right: 10px;
            width: 20px;
        }

        .login-right {
            background: white;
            padding: 50px 45px;
            flex: 1;
            min-width: 350px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-section {
            margin-bottom: 25px;
        }

        .logo-section img {
            max-width: 80px;
            height: auto;
            margin-bottom: 15px;
        }

        .login-header h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c5f7c;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
        }

        .form-group-wrapper {
            margin-bottom: 20px;
        }

        .form-group-wrapper label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .input-group-custom {
            position: relative;
        }

        .input-group-custom i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.1rem;
        }

        .input-group-custom input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .input-group-custom input:focus {
            outline: none;
            border-color: #2c5f7c;
            box-shadow: 0 0 0 3px rgba(44, 95, 124, 0.1);
        }

        .input-group-custom input::placeholder {
            color: #999;
        }

        .form-check {
            margin-bottom: 25px;
        }

        .form-check input {
            border-radius: 4px;
            cursor: pointer;
        }

        .form-check label {
            margin-bottom: 0;
            margin-left: 8px;
            cursor: pointer;
            color: #555;
            font-weight: 500;
        }

        .forget-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .forgot-link {
            color: #2c5f7c;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #1e3a52;
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #2c5f7c 0%, #1e3a52 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(44, 95, 124, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .register-section {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }

        .register-section a {
            color: #2c5f7c;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-section a:hover {
            color: #1e3a52;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
            border: none;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }

        .alert ul {
            margin-bottom: 0;
        }

        .alert ul li {
            margin: 5px 0;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-left {
                min-width: 100%;
                padding: 40px 30px;
            }

            .login-right {
                min-width: 100%;
                padding: 40px 30px;
            }

            .login-left h2 {
                font-size: 1.5rem;
            }

            .login-header h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Section -->
        <div class="login-left">
            <div>
                <h2>Welcome to</h2>
                <h2 style="color: #4CAF50; margin-bottom: 20px;">{{ $settings->title }}</h2>
                <p>Your Complete Inventory Management Solution</p>
                
                <ul class="inventory-features">
                    <li><i class="fas fa-box-open"></i>Smart Inventory Tracking</li>
                    <li><i class="fas fa-chart-line"></i>Real-time Analytics</li>
                    <li><i class="fas fa-users"></i>Multi-user Management</li>
                    <li><i class="fas fa-lock"></i>Secure & Reliable</li>
                    <li><i class="fas fa-mobile-alt"></i>Mobile Friendly</li>
                </ul>
            </div>
        </div>

        <!-- Right Section -->
        <div class="login-right">
            <div class="login-header">
                <div class="logo-section">
                    <img src="{{ asset('storage/') }}{{ !empty($settings->logo) ? $settings->logo : '' }}" alt="Logo">
                </div>
                <h3>Login</h3>
                <p>Manage your inventory efficiently</p>
            </div>

            <!-- Session Error -->
            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="form-group-wrapper">
                    <label for="email">Email / Username</label>
                    <div class="input-group-custom">
                        <i class="fas fa-envelope"></i>
                        <input type="text" id="email" name="email" 
                            class="@error('email') is-invalid @enderror" 
                            value="{{ old('email') }}" 
                            placeholder="Enter your email or username" 
                            required autofocus>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-group-wrapper">
                    <label for="password">Password</label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" 
                            class="@error('password') is-invalid @enderror" 
                            placeholder="Enter your password" 
                            required>
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="forget-container">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login to System
                </button>

                <!-- Register Link -->
                <div class="register-section">
                    Don't have an account? <a href="{{ route('register') }}">Register here</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
