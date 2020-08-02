Hello {{$user->name}}
You changed your email address so . Please verify your new email using this link :
{{route('verify',$user->verification_token)}}