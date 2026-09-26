<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>My Account | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main style="min-height: 100svh; display: grid; place-items: center; padding: 24px;">
            <section aria-labelledby="account-title" style="width: 100%; max-width: 520px; padding: 28px; background: white; border: 1px solid #d8e1e3; border-radius: 8px;">
                <p class="form-note">Barangay Information System</p>
                <h1 id="account-title" style="font-size: 26px; margin: 8px 0;">My account</h1>

                @if (session('warning'))
                    <div role="alert" style="padding: 12px; margin: 18px 0; background: #fff5df; color: #704800; border-radius: 6px;">{{ session('warning') }}</div>
                @endif
                @if (session('success'))
                    <div role="status" style="padding: 12px; margin: 18px 0; background: #eaf5eb; color: #245838; border-radius: 6px;">{{ session('success') }}</div>
                @endif

                <div style="padding: 18px; margin: 20px 0; background: #f5f8f5; border: 1px solid #e1e7de; border-radius: 6px;">
                    <p style="margin: 0 0 10px;"><strong>Name:</strong> {{ $user->name }}</p>
                    <p style="margin: 0;"><strong>Email:</strong> {{ $user->email }}</p>
                </div>

                @if ($user->role === 'staff')
                    <a href="{{ route('dashboard') }}" class="button button-primary" style="display: inline-block; text-decoration: none;">Open staff workspace</a>
                @elseif ($user->resident_id === null)
                    <p class="form-note" style="margin-bottom: 16px;">This account is not linked to a verified resident record. Ask barangay staff for a registration code, then enter it below.</p>
                    <form method="POST" action="{{ route('account.verify') }}">
                        @csrf
                        <div style="margin-bottom: 16px;">
                            <x-form.label for="registration_code" required>Registration code</x-form.label>
                            <x-form.input name="registration_code" required autocomplete="off" placeholder="XXXX-XXXX-XXXX-XXXX" />
                            <x-form.error :message="$errors->first('registration_code')" />
                        </div>
                        <button type="submit" class="button button-primary">Verify resident record</button>
                    </form>
                @else
                    <p class="form-note" style="margin-bottom: 20px;">Your account is linked to a verified resident record. Barangay record management is available to staff accounts.</p>
                @endif

                <form method="POST" action="{{ route('logout') }}" style="margin-top: 20px;">
                    @csrf
                    <button type="submit" class="button">Log out</button>
                </form>
            </section>
        </main>
    </body>
</html>
