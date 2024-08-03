@component('mail::message')
Activate your account!
# Hello {{ucwords($name)}}

For activating your account we have successfully generated OTP.

{{$otp}}

Kindly use this OTP for login your account.


Thanks,<br>
{{ config('app.name') }}
@endcomponent
