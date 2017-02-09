{{--模板大厅菜单模板--}}


<style>
    .curr { color:orangered; }
</style>
<div class="attr" style="text-align:right">
    <a href="{{Session::get('user')?DOMAIN.'order':DOMAIN.'o'}}"
        @if(explode('/',$_SERVER['REQUEST_URI'])[1]=='o')class="curr"@endif
        ><b>渲染列表</b></a>
    <a @if(Session::get('user')) href="{{DOMAIN}}u/finish"
        @else href="javascript:;" onclick="alert('您还没有登录！')"
        @endif><b>我的成品</b></a>
    <a @if(Session::get('user')) href="{{DOMAIN}}u/product"
        @else href="javascript:;" onclick="alert('您还没有登录！')"
        @endif><b>我的创作</b></a>
    <a href="{{DOMAIN}}"
        @if(in_array(explode('/',$_SERVER['REQUEST_URI'])[1],['','t']))class="curr"@endif
        ><b>模板大厅</b></a>
</div>