@extends('layout.main')
@section('content')
    <div class="online_list">
        <div style="margin-bottom:20px;text-align:center;color:dimgray;" id="promptInfo">
            <b style="font-size:20px;">在这里，您就是生活的导演...</b>
            <script>
                //信息5秒后隐藏
                setInterval('promptInfo()',1000*5);
                function promptInfo(){ $('#promptInfo').hide(100); }
            </script>
        </div>
        <div class="condition">
            @include('home.common.menu')
            <div class="attr" style="height:{{count($model['cates'])>13?40:20}}px;">
                <div class="cate_s">类型：</div>
                <div class="{{$cate==0?'cate_curr':'cate_s'}}" onclick="jump(0)">所有</div>
                @foreach($model['cates'] as $kcate=>$vcate)
                <div class="{{$cate==$kcate?'cate_curr':'cate_s'}}" onclick="jump({{$kcate}})">{{ $vcate }}</div>
                @endforeach
            </div>
        </div>

        <div class="list">
            @if(count($datas))
                @foreach($datas as $kdata=>$data)
            <a href="{{DOMAIN}}t/{{$data['id']}}" title="点击查看 {{$data['name']}} 的详情">
                <div class="prolist">
                    <div class="pro_one">
                        <img src="{{$data['thumb']}}" width="210">
                    </div>
                    <div class="pname"><b>{{ $data['name'] }}</b>
                        <div class="small">{{ $data['createTime'] }}</div>
                    </div>
                </div>
            </a>
                @endforeach
            @endif
            @for($i=0;$i<$pagelist['limit']-count($datas);++$i)
            <a href="javascript:void(0);">
                <div class="prolist">
                    <div class="pro_one">+待添加</div>
                    <div class="pname"><b>产品名称</b>
                        <div class="small">时间</div>
                    </div>
                </div>
            </a>
            @endfor

            <div style="clear:both;">@include('layout.page')</div>
        </div>
    </div>

    <script>
        function jump(cate){
            if (cate==0) {
                window.location.href = '{{DOMAIN}}';
            } else {
                window.location.href = '{{DOMAIN}}s/'+cate;
            }
        }
    </script>
@stop