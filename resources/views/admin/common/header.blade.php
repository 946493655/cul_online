{{--系统后台头模板--}}

<div class="header">
    <div class="header_text">
      <span>
          <div class="head_left">
              @if(Session::has('admin'))
                  <a href="{{DOMAIN}}u/product" style="color:orangered;">{{ Session::get('admin.username') }}系统管理</a> &nbsp;
                  <a href="{{DOMAIN}}admin/dologout" style="color:orangered;">退出</a>
              @else
                  <a href="{{DOMAIN}}admin" style="color:orangered;">管理员登录</a>
              @endif
          </div>
      </span>
      <span class="header_right">
        <div class="head_right">
            @if(Session::has('admin'))
            <a href="{{env('DOMAIN')}}" target="_blank">网站前台</a>
            @endif
            <a href="{{env('TALK_DOMAIN')}}" target="_blank">话题论坛</a>
            <a href="{{env('WWW_DOMAIN')}}/admin" target="_blank">主网站后台</a>
        </div>
      </span>
    </div>
</div>