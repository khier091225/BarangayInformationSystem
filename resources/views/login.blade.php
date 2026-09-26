<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sign In | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main style="min-height: 100svh; display: grid; place-items: center; padding: 24px;">
            <section aria-labelledby="login-title" style="width: 100%; max-width: 440px; padding: 28px; background: white; border: 1px solid #d8e1e3; border-radius: 8px;">
                <p class="form-note">Barangay Information System</p>
                <h1 id="login-title" style="font-size: 26px; margin: 8px 0;">Staff sign in</h1>
                <p class="form-note" style="margin-bottom: 24px;">Enter your account details to access barangay records.</p>

                @if (session('warning'))
                    <div role="alert" style="padding: 12px; margin-bottom: 20px; background: #fff5df; color: #704800; border-radius: 6px;">{{ session('warning') }}</div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div style="margin-bottom: 18px;">
                        <x-form.label for="email" required>Email address</x-form.label>
                        <x-form.input name="email" type="email" :value="old('email')" required autofocus autocomplete="username" />
                        @error('email')
                            <x-form.error :message="$message" />
                        @enderror
                    </div>
                    <div style="margin-bottom: 24px;">
                        <x-form.label for="password" required>Password</x-form.label>
                        <x-form.input name="password" type="password" required autocomplete="current-password" />
                        @error('password')
                            <x-form.error :message="$message" />
                        @enderror
                    </div>
                    <button type="submit" class="button button-primary" style="width: 100%;">Sign in</button>
                </form>
            </section>
        </main>
    </body>
</html>
