<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In • HR Management System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Font: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            background: #ffffff;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding:1rem;
            text-align: center;
        }
        .login-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        .login-header p {
            opacity: 0.95;
            font-size: 1rem;
            margin-top: 0.75rem;
        }
        .login-body {
            padding: 2.5rem;
        }
        .form-control {
            border-radius: 14px;
            padding: 0.9rem 1.2rem;
            border: 2px solid #e2e8f0;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.2);
        }
        .input-group-text {
            border-radius: 14px 0 0 14px;
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            border-right: none;
        }
        .btn-login {
            border-radius: 14px;
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.05rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            transition: all 0.4s ease;
        }
        .btn-login:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }
        .company-logo {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255,255,255,0.2);
        }
        .company-logo img {
            max-width: 85%;
            max-height: 85%;
            border-radius: 10px;
        }
        .hint-text {
            font-size: 0.875rem;
            color: #64748b;
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Header -->
    <div class="login-header">
        <div class="company-logo">
            <img src="{{ asset('iCB.png') }}" alt="Company Logo">
        </div>
        <h1>HR Management System</h1>
    </div>

    <!-- Body -->
    <div class="login-body">

        <!-- Error Message -->
        @if ($errors->has('username'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Login Failed:</strong> {{ $errors->first('username') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Success Message (after logout) -->
        @if (session('status'))
            <div class="alert alert-success rounded-3 d-flex align-items-center mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username / Employee Code Field -->
            <div class="mb-4">
                <label for="username" class="form-label fw-semibold text-dark">
                    <i class="bi bi-person-badge me-2"></i>
                    Username
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control form-control-lg @error('username') is-invalid @enderror" 
                        value="{{ old('username') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        placeholder="Enter Username"
                        style="text-transform: none;">
                </div>
                @error('username')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold text-dark">
                    <i class="bi bi-shield-lock me-2"></i>
                    Password
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control form-control-lg" 
                        required 
                        autocomplete="current-password"
                        placeholder="Enter your password">
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-muted" for="remember">
                        Remember me
                    </label>
                </div>
                <a href="#" class="text-decoration-none fw-medium" style="color: #667eea;">
                    Forgot Password?
                </a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-lg w-100 btn-login text-white">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Sign In
            </button>
        </form>

        <div class="text-center mt-4 text-muted small">
            © {{ date('Y') }} HR Management System. All rights reserved.
        </div>
    </div>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>