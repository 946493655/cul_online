{{--用户房间菜单模板--}}


<style>
    .curr { color:orangered; }
</style>
<div class="condition">
    <div class="attr" style="text-align:right;">
        <a href="{{DOMAIN}}myinfo"
           @if(Session::has('user')&&explode('/',$_SERVER['REQUEST_URI'])[1]=='myinfo')class="curr"@endif
            >我的账户</a>
        <a href="{{DOMAIN}}o"
           @if(Session::has('user')&&explode('/',$_SERVER['REQUEST_URI'])[1]=='o')class="curr"@endif
            ><b>渲染列表</b></a>
        <a href="{{DOMAIN}}myworks"
            @if(Session::has('user')&&explode('/',$_SERVER['REQUEST_URI'])[1]=='myworks')class="curr"@endif
            ><b>我的成品</b></a>
        <a href="{{DOMAIN}}u/product"
            @if(Session::has('user')&&explode('/',$_SERVER['REQUEST_URI'])[1]=='u')class="curr"@endif
            ><b>我的创作</b></a>
        <a href="{{DOMAIN}}"><b>模板大厅</b></a>
    </div>
    @if(isset($model)&&isset($cate))
    <div class="attr" style="height:{{count($model['cates'])>14?40:20}}px;">
        <div class="cate_s">类型：</div>
        <div class="{{$cate==0?'cate_curr':'cate_s'}}" onclick="jump(0)">所有</div>
        @foreach($model['cates'] as $kcate=>$vcate)
            <div class="{{$cate==$kcate?'cate_curr':'cate_s'}}" onclick="jump({{$kcate}})">{{ $vcate }}</div>
        @endforeach
    </div>
    @endif
    {{--支付二维码--}}
    @if(Session::has('user')&&explode('/',$_SERVER['REQUEST_URI'])[1]=='o')
    <div class="attr" style="height:20px">
        <span style="color:orangered;float:right;">支付二维码
            <a href="javascript:;" onclick="$('#qrcode').show()"><b>点击获取</b></a>
        </span>
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