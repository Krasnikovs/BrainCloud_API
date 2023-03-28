@component('mail::message')
    # Brain-Cloud.uk password reset

    Dear {{$email}}

    You are receiving this notification because you have (or someone pretending to be you has) requested a new password be sent for your account on "BrainCloud". If you did not request this notification then please ignore it, if you keep receiving it please contact the board administrator.

    you will be able to login using the following password:

    Password:
    {{$password}}


    You can of course change this password yourself via the profile page. If
    you have any difficulties please contact the board administrator.

    Thanks,
    Brain Cloud
@endcomponent
