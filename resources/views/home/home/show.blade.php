@extends('layout.main')
@section('content')
    <div class="online_list">
        <div class="condition">
            @include('home.home.menu')
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
                    @if($data['linkType']==4)<a href="{{$data['link']}}" target="_blank">@endif
                    <div id="toplay" title="点击播放视频" onclick="getPlay()"><img src="{{$data['thumb']}}"></div>
                    @if($data['linkType']==4)</a>@endif
                    <input type="hidden" name="linkType" value="{{$data['linkType']}}">
                    <input type="hidden" name="link" value="{{$data['link']}}">
                    <input type="hidden" name="id" value="{{$data['id']}}">
                    <input type="hidden" name="uid" value="{{Session::has('user')?Session::get('user.uid'):0}}">
                    <div id="gettemp" onclick="getTemp()">获取此模板</div>
                </td></tr>
        </table>
    </div>

    <script>
        function getPlay(){
            var linkType = $("input[name='linkType']").val();
            var link = $("input[name='link']").val();
            if (linkType!=4) {
                $("#toplay").html(link);
            } else {
                window.location.href = link;
            }
        }
        function getTemp(){
            var id = $("input[name='id']").val();
            var uid = $("input[name='uid']").val();
            if (uid==0) { alert('还没有登录！');return; }
            window.location.href = '{{DOMAIN}}u/product/apply/'+id;
        }
    </script>
@stop