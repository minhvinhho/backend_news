<h1>Xin chào{{ $details['title'] }}</h1>
<p>Đây là thư xác nhận gmail của bạn.</p>
{{--<p>{{ $details['token'] }}</p>--}}
<a href="http://localhost:8080/verify-email/{{$details['token']}}"><button type="button">XÁC THỰC</button></a>
