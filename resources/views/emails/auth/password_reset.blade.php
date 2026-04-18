@component('mail::message')
# Password Reset

You are receiving this email because we received a password reset request for your account.

Your password reset code is: **{{ $token }}**

If you did not request a password reset, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
