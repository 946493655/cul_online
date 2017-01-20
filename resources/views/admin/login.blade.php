<!DOCTYPE html>
<html>
<head>
    <title>文化之创作</title>
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="{{PUB}}assets/images/icon.png">
    <link rel="stylesheet" type="text/css" href="{{PUB}}assets/css/home.css">
    <style>
        .login { padding:20px;width:300px;border:1px solid lightgrey;border-radius:5px;box-shadow:0 0 5px lightgrey;
            position:fixed;left:40%;top:200px;  }
        .login_div { padding:5px; }
        .login_div input { padding:5px;width:280px;border:0;border-bottom:1px solid lightgrey;text-align:center;
            font-size:20px;font-family:'微软雅黑';outline:none; }
        #submit {  }
        #submit input { margin:auto 100px;padding:5px;width:100px;border:1px solid #0099cc;
            font-size:16px;font-weight:bold;color:white;background:#3399cc;cursor:pointer; }
    </style>
</head>
<body>
    @include('admin.common.header')
    <div class="login">
        <form action="/admin/dologin" method="POST" data-am-validator>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <div class="login_div login_first"><input type="text" placeholder="管理员名称" minlength="2" maxlength="20" name="uname" required></div>
            <div class="login_div"><input type="password" placeholder="密码(至少5位)" minlength="5" maxlength="20" name="password" required></div>
            <div>&nbsp;</div>
            <div id="submit"><input type="submit" value="登 录"></div>
        </form>
    </div>
</body>
</html>