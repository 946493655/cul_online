@extends('layout.main')
@section('content')
    <div class="online_list">
        <div class="condition">
            @include('home.common.menu')
        </div>

        <table class="list">
            <tr><td colspan="2">模板编号：{{$data['serial']}}</td></tr>
            <tr>
                <td>模板名称：{{$data['name']}}</td>
                <td>模板类型：{{$data['cateName']}}</td>
            </tr>
            <tr><td colspan="2">模板介绍：{{$data['intro']}}</td></tr>
            <tr><td colspan="2"><span style="float:right;">发布时间：{{$data['createTime']}}</span></td></tr>
            <tr><td colspan="2" style="border:0;">
                    <div id="toplay">
                        @if($data['thumb'])
                        <a @if(in_array($data['linkType'],[1,4])) href="{{$data['link']}}" target="_blank"
                           @elseif(in_array($data['linkType'],[2,3])) href="javascript:;" onclick="getPre()"
                           @endif
                           @if($data['link']) title="点击播放{{$data['name']}}" @endif
                           ><img src="{{$data['thumb']}}">
                        </a>
                        @else <a href="javascript:;" title="暂无视频" onclick="alert('暂无视频！')">暂无缩略图</a>
                        @endif
                    </div>
                    <div class="editpro" style="margin-top:20px;text-align:center;">
                        <a @if(in_array($data['linkType'],[1,4])) href="{{$data['link']}}" target="_blank"
                           @else href="javascript:;" onclick="getPre()"
                           @endif
                           @if($data['link']) title="点击播放{{$data['name']}}" @endif
                           class="gettemp">播放</a>
                        <a href="javascript:void(0);" class="gettemp" title="点击获取该产品" onclick="getTemp()">获取此模板</a>
                    </div>
                </td></tr>
        </table>
    </div>
    <input type="hidden" name="link" value="{{$data['link']}}">
    <input type="hidden" name="id" value="{{$data['id']}}">
    <input type="hidden" name="uid" value="{{Session::has('user')?Session::get('user.uid'):0}}">

    {{--弹出框：播放--}}
    <div class="editproduct" id="getpre">
        <div class="mask"></div>
        <div id="preview" style="padding:10px;width:530px;height:400px;position:fixed;left:30%;top:220px;"></div>
    </div>
    {{--弹出框：修改信息--}}
    <div class="editproduct" id="gettemp">
        <div class="mask"></div>
        <form action="{{DOMAIN}}u/product/apply" method="POST" data-am-validator enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="tempid" value="{{$data['id']}}">
            <p style="text-align:center"><b>{{$data['name']}} 产品信息更新</b></p>
            <p>产品名称：
                <input type="text" minlength="2" maxlength="20" required name="name" value="{{$data['name']}}">
            </p>
            <p>产品介绍：
                <textarea rows="5" style="resize:none;" name="intro">{{$data['intro']}}</textarea>
            </p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="$('#gettemp').hide()"> X </a>
        </form>
    </div>

    <script>
        function getPre(){
            var link = $("input[name='link']").val();
            var kaiguan = '<a href="javascript:void(0);" onclick="getClose()" title="点击关闭播放" style="padding:10px;color:white;text-decoration:none;background:red;position:absolute;">关闭</a>';
            $("#preview").html(link+kaiguan);
            $('#getpre').show();
        }
        function getClose(){ window.location.href = ''; }
        function getTemp(){
            var id = $("input[name='id']").val();
            var uid = $("input[name='uid']").val();
            if (uid==0) { alert('还没有登录！');return; }
            {{--window.location.href = '{{DOMAIN}}u/product/apply/'+id;--}}
            $("#gettemp").show();
        }
    </script>
@stop