@component('mail::message')
Dear <b>{{ $name }}</b>,<br><br>
Please click the button below to proceed with password recovery. This link will be valid for 30 Minutes.

@component('mail::button', ['url' => $url])
    Recover Password
@endcomponent

<br>
If you did not request a password reset, kindly contact the system administrator.
@endcomponent
