{{--菜单模板--}}


<div class="attr" style="text-align:right">
    <a href="{{DOMAIN}}order"><b>渲染列表</b></a>
    <a href="{{DOMAIN}}u/finish"><b>我的成品</b></a>
    <a href="{{DOMAIN}}u/product" style="
    {{--color:{{isset(explode('/',$_SERVER['REQUEST_URI'])[2])?'orangered':'grey'}};--}}
            "><b>我的创作</b>
    </a>
    <a href="{{DOMAIN}}u" style="
            color:{{isset(explode('/',$_SERVER['REQUEST_URI'])[2])?'grey':'orangered'}};
            "><b>作品大厅</b>
    </a>
</div>