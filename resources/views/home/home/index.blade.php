@extends('home.main')
@section('content')
    <div class="online_list" style="height:900px;background:rgb(40,40,40);">
        <div style="margin-bottom:20px;text-align:center;color:dimgray;" id="promptInfo">
            <b>在这里，您就是下一位导演...</b>
            <script>
                //信息10秒后隐藏
                setInterval('promptInfo()',1000*5);
                function promptInfo(){ $('#promptInfo').hide(200); }
            </script>
        </div>
        <div class="condition">
            @include('home.home.menu')
            <div class="attr" style="height:{{count($model['cates'])>14?40:20}}px;">
                <div class="cate_s">模板类型：</div>
                {{--<a href="" class="{{$cate==0?'curr':''}}">全部</a>--}}
                {{--@foreach($model['cates'] as $kcate=>$vcate)--}}
                    {{--<a href="" class="{{$cate==$kcate?'curr':''}}">{{ $vcate }}</a>--}}
                {{--@endforeach--}}
                @foreach($model['cates'] as $kcate=>$vcate)
                <div class="cate_s {{$cate==$kcate?'curr':''}}">{{ $vcate }}</div>
                @endforeach
            </div>
        </div>

        <div class="list">
            @if(count($datas)>1)
                @foreach($datas as $kdata=>$data)
                    @if(is_numeric($kdata))
            <a href="/h/{{$data['id']}}" target="_blank" title="{{$data['name']}}">
                <div class="prolist">
                    <div class="pro_one">
                        <img src="{{$data['thumb']}}" width="210">
                    </div>
                    <div class="pname"><b>{{ $data['name'] }}</b>
                        <div class="small">{{ $data['createTime'] }}</div>
                    </div>
                </div>
            </a>
                    @endif
                @endforeach
            @endif
            @for($i=0;$i<$datas['pagelist']['limit']+1-count($datas);++$i)
            <a href="javascript:void(0);">
                <div class="prolist">
                    <div class="pro_one">+待添加</div>
                    <div class="pname"><b>产品名称</b>
                        <div class="small">时间</div>
                    </div>
                </div>
            </a>
            @endfor

            <div style="clear:both;"></div>
            <div style="margin-top:20px;">@include('layout.page')</div>
        </div>
    </div>
@stop