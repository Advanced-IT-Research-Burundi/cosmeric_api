<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Basic email-safe styles */
        body { margin:0; padding:0; background:#f5f7fb; font-family: Arial, Helvetica, sans-serif; color:#1f2937; }
        .wrapper { width:100%; background:#f5f7fb; padding:24px 0; }
        .container { max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
        .header { background:#111827; color:#ffffff; padding:20px 24px; font-size:18px; font-weight:bold; }
        .content { padding:24px; line-height:1.55; font-size:15px; }
        .btn { display:inline-block; background:#2563eb; color:#ffffff !important; text-decoration:none; padding:10px 16px; border-radius:6px; font-weight:600; }
        .muted { color:#6b7280; font-size:13px; }
        .footer { padding:16px 24px; text-align:center; color:#6b7280; font-size:12px; }
        a { color:#2563eb; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                {{ config('app.name', 'CASOMIREC') }}
            </div>
            <div class="content">
                <p>Hi {{ $user->prenom ?? $user->name ?? 'there' }},</p>

                <p>Welcome to {{ config('app.name', 'CASOMIREC') }}! We’re excited to have you on board.</p>

                <p>Here’s a quick summary of your account:</p>
                <ul>
                    <li><strong>Name:</strong> {{ $user->name ?? trim(($user->nom ?? '') . ' ' . ($user->prenom ?? '')) }}</li>
                    @if(!empty($user->email))
                        <li><strong>Email:</strong> {{ $user->email }}</li>
                    @endif
                    @if(!empty($user->telephone))
                        <li><strong>Phone:</strong> {{ $user->telephone }}</li>
                    @endif
                </ul>

                <p>You can sign in anytime to explore your dashboard and manage your profile.</p>

                <p style="margin:20px 0;">
                    <a class="btn" href="{{ url('/') }}" target="_blank" rel="noopener">Go to Dashboard</a>
                </p>

                <p class="muted">If the button doesn’t work, copy and paste this link into your browser:<br>
                    <a href="{{ url('/') }}" target="_blank" rel="noopener">{{ url('/') }}</a>
                </p>

                <p>If you didn’t create this account, please ignore this email or contact support.</p>

                <p>Cheers,<br>
                The {{ config('app.name', 'CASOMIREC') }} Team</p>
            </div>
            <div class="footer">
                © {{ now()->year }} {{ config('app.name', 'CASOMIREC') }}. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
