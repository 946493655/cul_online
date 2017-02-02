{{--用户房间菜单模板--}}


<div class="condition">
    <div class="attr" style="text-align:right">
        <a href="{{DOMAIN}}admin/order/torender" @if(isset($curr)&&$curr=='torender')style="color:orangered;"@endif><b>渲染列表</b></a>
        <a href="{{DOMAIN}}admin/order/finish" @if(isset($curr)&&$curr=='finish')style="color:orangered;"@endif><b>用户成品</b></a>
        <a href="{{DOMAIN}}admin/product" @if(isset($curr)&&$curr=='product')style="color:orangered;"@endif><b>用户创作</b></a>
        <a href="{{DOMAIN}}admin/temp"
           @if((isset($curr)&&$curr=='temp') || (explode('/',$_SERVER['REQUEST_URI'])['2']=='t'))
                style="color:orangered;"
                @endif
        ><b>模板列表</b>
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