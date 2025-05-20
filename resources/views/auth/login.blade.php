<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        body {
            background: url('{{ asset('images/background.AVIF') }}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }

        .card-login {
            border-radius: 1rem;
            overflow: hidden;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #2575fc;
        }

        .btn-login {
            background-color: #2575fc;
            border: none;
            transition: background-color .3s;
        }

        .btn-login:hover {
            background-color: #1a5bb8;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #999;
        }

        .form-input-with-icon {
            position: relative;
        }

        .form-input-with-icon .form-control {
            padding-left: 2.5rem;
        }
    </style>
</head>

<body>
    <div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg card-login">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4 text-primary">🌟 Đăng Nhập 🌟</h3>

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf

                        <div class="mb-3 form-input-with-icon">
                            <i class="bi bi-person-fill input-icon"></i>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}" placeholder="Tên tài khoản"
                                required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-input-with-icon">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Mật khẩu" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label text-secondary" for="remember">Ghi nhớ đăng nhập</label>
                        </div> --}}

                        <div class="d-grid">
                            <button type="submit" class="btn btn-login btn-lg text-white">Đăng nhập</button>
                        </div>

                        {{-- @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a href="{{ route('password.request') }}"
                                    class="text-decoration-none text-white-50">Quên
                                    mật khẩu?</a>
                            </div>
                        @endif --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
</body>

</html>
