{{--用户房间菜单模板--}}


<div class="condition">
    <div class="attr" style="text-align:right">
        <a href="{{DOMAIN}}admin/order/torender"><b>渲染列表</b></a>
        <a href="{{DOMAIN}}admin/order/finish" style="
            @if(Session::has('admin') && $_SERVER['REQUEST_URI']=='/admin/order/finish')
                color:orangered;
            @else
                color:grey;
            @endif
            "><b>用户成品</b></a>
        <a href="{{DOMAIN}}admin/product" style="
            @if(Session::has('admin') && $_SERVER['REQUEST_URI']=='/admin/product')
                color:orangered;
            @else
                color:grey;
            @endif
            "><b>用户创作</b>
        </a>
        <a href="{{DOMAIN}}admin/temp" style="
            @if(Session::has('admin') && $_SERVER['REQUEST_URI']=='/admin/temp')
                color:orangered;
            @else
                color:grey;
            @endif
            "><b>模板列表</b>
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