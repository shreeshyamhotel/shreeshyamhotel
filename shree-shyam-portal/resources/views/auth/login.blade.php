<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Shree Shyam Hotel & Restorent</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo_round.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo_round.png') }}">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        body.login-body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: radial-gradient(circle at 50% 50%, #111827 0%, #030712 100%);
            padding: 1.5rem;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            background: rgba(18, 24, 38, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-xl);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), var(--shadow-glow);
            animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.25rem;
        }

        .login-logo {
            width: 58px;
            height: 58px;
            border-radius: var(--radius-lg);
            background: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .login-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .login-header h2 {
            font-family: var(--font-title);
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }

        .login-header p {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .input-icon-group {
            position: relative;
        }

        .input-icon-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: var(--transition-fast);
        }

        .input-icon-group .form-control {
            padding-left: 2.75rem;
        }

        .input-icon-group .form-control:focus + i {
            color: #818cf8;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            cursor: pointer;
        }

        .checkbox-label input {
            cursor: pointer;
            accent-color: var(--accent-color);
        }
    </style>
</head>
<body class="login-body">

    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <img src="{{ asset('logo_round.png') }}" alt="Shree Shyam Logo">
            </div>
            <h2>Shree Shyam Hotel & Restorent</h2>
            <p>Enter email and password to access the panel</p>
        </div>

        <form id="login-form" action="{{ route('login.post') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-icon-group">
                    <input type="email" name="email" id="email" class="form-control" placeholder="shyamhotel@gmail.com" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <i class="fa-regular fa-envelope"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                    <i class="fa-solid fa-lock"></i>
                </div>
            </div>

            <div class="remember-forgot">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem 1.5rem; margin-top: 0.5rem;">
                <i class="fa-solid fa-right-to-bracket"></i> Login Account
            </button>
        </form>
    </div>

    <!-- jQuery & validation plugins -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <!-- SweetAlert2 Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Validation
            $("#login-form").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true
                    }
                },
                messages: {
                    email: {
                        required: "Please enter your registered email address.",
                        email: "Please enter a valid email address format."
                    },
                    password: {
                        required: "Please enter your password."
                    }
                }
            });

            // Display Controller errors using SweetAlert2
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: "{{ $errors->first() }}",
                    background: '#121826',
                    color: '#fff',
                    confirmButtonColor: '#f43f5e'
                });
            @endif

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Notification',
                    text: "{{ session('success') }}",
                    background: '#121826',
                    color: '#fff',
                    confirmButtonColor: '#10b981',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        });
    </script>
</body>
</html>
