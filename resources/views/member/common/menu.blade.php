{{--用户房间菜单模板--}}


<div class="condition">
    <div class="attr" style="text-align:right">
        <a href="{{DOMAIN}}order"><b>渲染列表</b></a>
        <a href="{{DOMAIN}}u/finish" style="
            @if(Session::has('user') && $_SERVER['REQUEST_URI']=='/u/finish')
                color:orangered;
            @else
                color:grey;
            @endif
            "><b>我的成品</b></a>
        <a href="{{DOMAIN}}u/product" style="
            @if(Session::has('user') && $_SERVER['REQUEST_URI']=='/u/product')
                color:orangered;
            @else
                color:grey;
            @endif
            "><b>我的创作</b>
        </a>
        <a href="{{DOMAIN}}"><b>作品大厅</b>
        </a>
    </div>
    @if(isset($model)&&isset($cate))
    <div class="attr" style="height:{{count($model['cates'])>13?40:20}}px;">
        <div class="cate_s">模板类型：</div>
        <div class="{{$cate==0?'cate_curr':'cate_s'}}" onclick="jump(0)">所有</div>
        @foreach($model['cates'] as $kcate=>$vcate)
            <div class="{{$cate==$kcate?'cate_curr':'cate_s'}}" onclick="jump({{$kcate}})">{{ $vcate }}</div>
        @endforeach
    </div>
    @endif
</div>

<script>
    function jump(cate){
        if (cate==0) {
            window.location.href = '{{DOMAIN}}u/product';
        } else {
            window.location.href = '{{DOMAIN}}u/product/s/'+cate;
        }
    }
</script>