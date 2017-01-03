@extends('home.main')
@section('content')
    <div class="online_list" style="height:900px;background:rgb(40,40,40);">
        <div class="condition">@include('home.home.menu')</div>

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
                    <div id="gettemp">获取此模板</div>
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
    </script>
@stop