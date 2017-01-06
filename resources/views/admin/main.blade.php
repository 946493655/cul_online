<!DOCTYPE html>
<html>
<head>
    <title>做视频-在线创作-系统后台</title>
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="{{PUB}}assets/images/icon.png">
    <link rel="stylesheet" type="text/css" href="{{PUB}}assets/css/home.css">
    <script src="{{PUB}}assets/js/jquery-1.10.2.min.js"></script>
</head>
<body>
@include('admin.common.header')

<div class="online_bg">
    @yield('content')
</div>

{{--@include('layout.footer')--}}
</body>
</html>