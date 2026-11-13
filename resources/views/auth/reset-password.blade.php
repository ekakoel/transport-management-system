<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | Bali Kami Tour TMS</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'DM Sans', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px;
            color: #102338;
            background:
                radial-gradient(ellipse at 12% 10%, #153a52 0, transparent 34%),
                radial-gradient(ellipse at 90% 88%, #103b46 0, transparent 32%),
                #071525;
        }
        .login-page { width: 100%; display: flex; justify-content: center; }
        .login-card {
            width: min(460px, 100%);
            background: #ffffff;
            border: 1px solid rgb(255 255 255 / 10%);
            border-radius: 28px;
            box-shadow: 0 40px 100px rgb(0 0 0 / 28%), 0 4px 20px rgb(0 0 0 / 10%);
            padding: 40px;
        }
        .login-brand { display: inline-flex; align-items: center; gap: 12px; margin-bottom: 30px; }
        .brand-mark { display: inline-flex; align-items: center; justify-content: center; width: 46px; height: 46px; border-radius: 14px; color: #fff; }
        .brand-mark img { width: 40px; height: 40px; }
        .brand-copy { display: flex; flex-direction: column; line-height: 1.2; }
        .brand-copy strong { font-size: 18px; letter-spacing: 0.08em; color: #071525; }
        .brand-copy small { font-size: 10px; letter-spacing: 0.22em; color: #718096; }
        .login-heading .welcome-label { display: inline-block; font-size: 11px; font-weight: 700; letter-spacing: 0.18em; color: #0caaa5; margin-bottom: 10px; }
        .login-heading h2 { margin: 0 0 8px; font-size: 30px; line-height: 1.25; color: #071525; font-weight: 800; }
        .login-heading p { margin: 0 0 24px; font-size: 14px; color: #718096; }
        .alert { padding: 12px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: #d1e7dd; border: 1px solid #198754; color: #146c43; }
        .alert-danger { background: #f8d7da; border: 1px solid #b02a37; color: #842029; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-weight: 700; margin-bottom: 6px; font-size: 14px; color: #102338; }
        .field input { width: 100%; padding: 12px; border: 1px solid #aaa; border-radius: 8px; font: inherit; font-size: 14px; }
        .field input:focus { outline: none; border-color: #24c9c3; box-shadow: 0 0 0 3px rgb(36 201 195 / 18%); }
        .field input.input-error { border-color: #d93850; }
        .field input[readonly] { background: #f3f5f7; color: #5b6b7b; }
        .field-error { color: #a51d35; font-size: 13px; display: block; margin-top: 6px; }
        .login-button { width: 100%; padding: 13px; border: 0; border-radius: 12px; background: #071525; color: #fff; font-weight: 700; font-size: 15px; cursor: pointer; }
        .login-button:hover { background: #0c2035; }
        .auth-links { margin: 18px 0 0; font-size: 13px; color: #718096; text-align: center; }
        .auth-links a { color: #0caaa5; font-weight: 600; text-decoration: none; }
        .auth-links a:hover { text-decoration: underline; }
        @media (max-width: 520px) { .login-card { padding: 28px; border-radius: 20px; } }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-brand">
                <span class="brand-mark">
                    <img src="{{ asset('images/bali_kami_icon.png') }}" alt="Logo Bali Kami">
                </span>
                <span class="brand-copy">
                    <strong>BALI KAMI TOUR</strong>
                    <small>TRANSPORT MANAGEMENT SYSTEM (TMS)</small>
                </span>
            </div>

            <div class="login-heading">
                <span class="welcome-label">RESET PASSWORD</span>
                <h2>Buat password baru<span>.</span></h2>
                <p>Masukkan password baru untuk akun Anda.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan.</strong>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="field">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email) }}"
                        placeholder="you@balikamitour.com" autocomplete="username" maxlength="190" required
                        class="{{ $errors->has('email') ? 'input-error' : '' }}">
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password Baru</label>
                    <input id="password" type="password" name="password" placeholder="Minimal 8 karakter"
                        autocomplete="new-password" required autofocus
                        class="{{ $errors->has('password') ? 'input-error' : '' }}">
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        placeholder="Ulangi password baru" autocomplete="new-password" required>
                </div>

                <button type="submit" class="login-button">
                    <span>Simpan Password Baru</span>
                </button>
            </form>

            <p class="auth-links"><a href="{{ route('login') }}">Kembali ke halaman login</a></p>
        </div>
    </div>
    @include('partials.password-toggle')
</body>
</html>
