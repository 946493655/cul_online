@extends('admin.main')
@section('content')
    <div class="online_list" style="height:1350px;">
        @include('admin.common.menu')

        <div class="searchlist">
            {{--@if(env('APP_ENV')=='local')--}}
                {{--<a href="javascript:void(0);" onclick="getEditPro2()">清空表</a>--}}
            {{--@endif--}}
            <a href="javascript:void(0);" onclick="getEditPro1()" title="新产品增加">+ 添加产品</a>
        </div>
        <div class="list">
            @if(count($datas))
                @foreach($datas as $kdata=>$data)
            <a href="{{DOMAIN}}admin/product/{{$data['id']}}" title="点击进入调整 {{$data['name']}}">
                <div class="prolist">
                    <div class="pro_one">
                        @if($data['thumb'])
                        <img src="{{$data['thumb']}}" width="210">
                        @else
                        缩略图待添加
                        @endif
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
    {{--弹出框：添加产品--}}
    <div class="editproduct" id="editproduct1">
        <div class="mask"></div>
        <form action="{{DOMAIN}}admin/product" method="POST" data-am-validator>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <p style="text-align:center"><b>产品添加</b></p>
            <p>产品名称：
                <input type="text" placeholder="产品名称" minlength="2" maxlength="20" required name="name">
            </p>
            <p>选择模板：
                <select name="tempid" required>
                    @foreach($temps as $temp)
                        <option value="{{$temp['id']}}">{{$temp['name']}}</option>
                    @endforeach
                </select>
            </p>
            {{--<p>产品类型：--}}
                {{--<select name="cate" required>--}}
                    {{--@foreach($model['cates'] as $k=>$vcate)--}}
                        {{--<option value="{{$k}}">{{$vcate}}</option>--}}
                    {{--@endforeach--}}
                {{--</select>--}}
            {{--</p>--}}
            <p>产品介绍：
                <textarea rows="5" placeholder="说明文字" style="resize:none;" name="intro"></textarea>
            </p>
            <p>用户名称：
                <input type="text" placeholder="用户名称" minlength="2" maxlength="20" required name="uname">
            </p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="getClose()"> X </a>
        </form>
    </div>
    {{--弹出框：删除产品--}}
    {{--<div class="editproduct" id="editproduct2">--}}
        {{--<div class="mask"></div>--}}
        {{--<div class="msg">--}}
            {{--<p style="text-align:center;">--}}
                {{--确定要删除 XX产品 么？ <br><br>--}}
                {{--<a href="{{DOMAIN}}admin/temp/clear">确定清空</a>--}}
                {{--<a href="javascript:void(0);" onclick="getClose()">取消</a>--}}
            {{--</p>--}}
        {{--</div>--}}
    {{--</div>--}}

    <script>
        function getEditPro1(){ $("#editproduct1").show(); }
//        function getEditPro2(){ $("#editproduct2").show(); }
        function getClose(){ $("#editproduct").hide(); }
    </script>
@stop