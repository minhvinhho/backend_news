<h1>Xin chào{{ $details['title'] }}</h1>
<p>Đây là thư đổi mật khẩu của bạn.</p>
{{--<p>{{ $details['token'] }}</p>--}}
<a href="http://localhost:8080/forget-password/{{$details['token']}}"><button type="button">ĐỔI MẬT KHẨU</button></a>
