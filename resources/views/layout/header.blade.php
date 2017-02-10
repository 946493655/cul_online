{{-- 前台首页头模板 --}}


<div class="header">
    <div class="header_text">
      <span>
        <div class="head_left">
            @if(Session::has('user'))
                <a href="{{DOMAIN}}u/product" style="color:orangered;">{{ Session::get('user.username') }}创作中心</a> &nbsp;
                <a href="{{DOMAIN}}login/logout" style="color:orangered;">退出</a>
            @else
                <a href="{{DOMAIN}}login" style="color:orangered;">用户在此登录</a>
            @endif
        </div>
      </span>
      <span class="header_right">
        <div class="head_right">
            <a href="{{DOMAIN}}help" target="_blank">帮助</a>
            <a href="{{env('TALK_DOMAIN')}}">话题论坛</a>
            <a href="{{env('WWW_DOMAIN')}}" target="_blank">主网站</a>
        </div>
        {{--<div class="head_right"><a href="/newuser">新手点这里</a></div>--}}
      </span>
    </div>
</div>