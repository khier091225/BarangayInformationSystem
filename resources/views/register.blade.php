<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Create Account | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main style="min-height: 100svh; display: grid; place-items: center; padding: 24px;">
            <section aria-labelledby="register-title" style="width: 100%; max-width: 440px; padding: 28px; background: white; border: 1px solid #d8e1e3; border-radius: 8px;">
                <p class="form-note">Barangay Information System</p>
                <h1 id="register-title" style="font-size: 26px; margin: 8px 0;">Create an account</h1>
                <p class="form-note" style="margin-bottom: 24px;">Ask barangay staff to verify your resident record and issue a registration code before signing up.</p>

                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <div style="margin-bottom: 18px;">
                        <x-form.label for="registration_code" required>Registration code</x-form.label>
                        <x-form.input name="registration_code" required autofocus autocomplete="off" placeholder="XXXX-XXXX-XXXX-XXXX" />
                        <x-form.error :message="$errors->first('registration_code')" />
                    </div>
                    <div style="margin-bottom: 18px;">
                        <x-form.label for="email" required>Email address</x-form.label>
                        <x-form.input name="email" type="email" :value="old('email')" required autocomplete="email" />
                        <x-form.error :message="$errors->first('email')" />
                    </div>
                    <div style="margin-bottom: 18px;">
                        <x-form.label for="password" required>Password</x-form.label>
                        <x-form.input name="password" type="password" required minlength="8" autocomplete="new-password" />
                        <x-form.error :message="$errors->first('password')" />
                    </div>
                    <div style="margin-bottom: 24px;">
                        <x-form.label for="password_confirmation" required>Confirm password</x-form.label>
                        <x-form.input name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" />
                    </div>
                    <button type="submit" class="button button-primary" style="width: 100%;">Create account</button>
                </form>

                <p class="form-note" style="margin-top: 22px; text-align: center;">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
            </section>
        </main>
    </body>
</html>
