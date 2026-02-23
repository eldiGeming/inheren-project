<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            overflow: hidden; /* Tidak bisa scroll */
            background-color: rgb(250, 250, 250); /* Abu-abu muda */
        }

        .login-section {
            height: 100vh;
        }

        .left-side {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 100%;
            max-width: 380px;
        }

        .logo {
            display: block;
            margin: 0 auto 30px auto;
        }

        .right-image {
            height: 100vh;
            object-fit: cover;
            border-top-left-radius: 30px;
            border-bottom-left-radius: 30px;
        }
    </style>
</head>
<body>

<section class="login-section">
    <div class="container-fluid h-100">
        <div class="row h-100">

            <!-- LEFT SIDE -->
            <div class="col-sm-6 left-side">
                <div class="login-box">

                    <!-- Logo Center -->
                    <img src="{{ asset('assets/images/logo-full.png') }}"
                         class="logo"
                         width="200">

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                    <h3 class="fw-bold mb-4 text-center text-primary">Please login to your account</h3>
                        <!-- Email -->
                        <div class="mb-4">
                            <input type="email"
                                   name="email"
                                   class="form-control form-control-lg"
                                   placeholder="Email address"
                                   value="{{ old('email') }}"
                                   required autofocus>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <input type="password"
                                   name="password"
                                   class="form-control form-control-lg"
                                   placeholder="Password"
                                   required>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Remember -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input">
                            <label class="form-check-label">Remember me</label>
                        </div>

                        <button class="btn btn-primary btn-lg w-100 mb-3" type="submit">
                            Login
                        </button>
                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <a class="text-muted small"
                                   href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            </div>
                        @endif
                    </form>

                </div>
            </div>

            <!-- RIGHT SIDE IMAGE -->
            <div class="col-sm-6 px-0 d-none d-sm-block">
                <img src="{{ asset('assets/images/auth/auth-user.jpg') }}"
                     alt="Login image"
                     class="w-100 right-image">
            </div>

        </div>
    </div>
</section>

</body>
</html>