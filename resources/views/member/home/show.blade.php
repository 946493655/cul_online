@extends('layout.main')
@section('content')
    <div class="online_list">
        @include('member.common.menu')

        <table class="list">
            <tr>
                <td>产品名称：{{$data['name']}}</td>
                <td>产品类型：{{$data['cateName']}}</td>
            </tr>
            <tr><td colspan="2">产品介绍：{{$data['intro']}}</td></tr>
            <tr><td colspan="2"><span style="float:right;">发布时间：{{$data['createTime']}}</span></td></tr>
            <tr><td colspan="2" style="border:0;">
                @if($data['thumb'])
                    <div id="toplay">
                        <a @if(in_array($data['linkType'],[1,4])) href="{{$data['link']}}" target="_blank"
                           @else href="javascript:;" onclick="getPre()"
                           @endif
                           @if($data['link']) title="点击播放{{$data['name']}}" @endif
                            ><img src="{{$data['thumb']}}">
                        </a>
                    </div>
                @else
                    <div id="toplay" title="没有缩略图，等待添加">
                        <p>没有缩略图，等待添加...
                            <a href="javascript:void(0);" style="color:orangered;" onclick="getThumb()">马上添加</a>
                        </p>
                    </div>
                @endif
                <div id="editpro">
                    <a href="javascript:void(0);" onclick="getReturnPre()">返回</a>
                    <a href="javascript:void(0);" onclick="getEditPro()">修改产品</a>
                    <a href="javascript:void(0);" onclick="getThumb()">缩略图</a>
                    <a href="javascript:void(0);" onclick="getLink()">视频链接</a>
                    <a @if(in_array($data['linkType'],[1,4])) href="{{$data['link']}}" target="_blank"
                       @else href="javascript:;" onclick="getPre()"
                       @endif
                       @if($data['link']) title="点击播放{{$data['name']}}" @endif
                       class="gettemp">播放</a>
                    <a href="{{DOMAIN}}u/pro/{{$data['id']}}/layer">修改动画</a>
                    <a href="javascript:void(0);" title="彻底删除该产品" onclick="getDel()">删除产品</a>
                </div>
            </td></tr>
        </table>
    </div>
    {{--<input type="hidden" name="linkType" value="{{$data['linkType']}}">--}}
    <input type="hidden" name="link" value="{{$data['link']}}">
    <input type="hidden" name="id" value="{{$data['id']}}">
    <input type="hidden" name="uid" value="{{Session::has('user')?Session::get('user.uid'):0}}">

    {{--弹出框：修改产品数据--}}
    <div class="editproduct" id="editproduct1">
        <div class="mask"></div>
        <form action="{{DOMAIN}}u/product/{{$data['id']}}" method="POST" data-am-validator>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <p style="text-align:center"><b>{{$data['name']}} 产品修改</b></p>
            <p>产品名称：
                <input type="text" minlength="2" maxlength="20" required name="name" value="{{$data['name']}}">
            </p>
            <p>产品介绍：
                <textarea rows="5" style="resize:none;" name="intro">{{$data['intro']}}</textarea>
            </p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="getClose()"> X </a>
        </form>
    </div>
    {{--弹出框：更新缩略图--}}
    <div class="editproduct" id="editthumb">
        <div class="mask"></div>
        <form action="{{DOMAIN}}u/product/link1/{{$data['id']}}" method="POST"
              data-am-validator enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="pro_id" value="{{$data['id']}}">
            <p style="text-align:center"><b>{{$data['name']}} 缩略图修改</b></p>
            <p>缩略图更新：<br>@include('layout.uploadimg')</p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="getClose()"> X </a>
        </form>
    </div>
    {{--弹出框：更新视频链接--}}
    <div class="editproduct" id="editlink">
        <div class="mask"></div>
        <form action="{{DOMAIN}}u/product/link2/{{$data['id']}}" method="POST"
              data-am-validator enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="pro_id" value="{{$data['id']}}">
            <p style="text-align:center"><b>{{$data['name']}} 链接修改</b></p>
            <p>视频链接类型：
                <select name="linkType" required>
                    @foreach($model['linkTypes'] as $klinkType=>$vlinkType)
                        <option value="{{$klinkType}}" {{$data['linkType']==$klinkType?'selected':''}}>
                            {{$vlinkType}}</option>
                    @endforeach
                </select>
            </p>
            <p>视频链接：
                <input type="text" placeholder="例：" minlength="2" required
                       name="link" value="{{$data['link']}}">
            </p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="getClose()"> X </a>
        </form>
    </div>
    {{--弹出框：播放--}}
    <div class="editproduct" id="getpre">
        <div class="mask"></div>
        <div id="play" style="padding:10px;width:530px;height:400px;position:fixed;left:30%;top:220px;"></div>
    </div>
    {{--弹出框：删除产品--}}
    <div class="editproduct" id="del">
        <div class="mask"></div>
        <div class="msg">
            <p style="text-align:center;">
                确定要删除 <b>{{$data['name']}}</b> 信息么？ <br><br>
                <a href="{{DOMAIN}}u/product/delete/{{$data['id']}}">确定删除</a>
                <a href="javascript:void(0);" onclick="closePro()">取消</a>
            </p>
        </div>
    </div>

    <script>
        function getReturnPre(){ window.location.href = '{{DOMAIN}}u/product'; }
        function getPre(){
            var link = $("input[name='link']").val();
            var kaiguan = '<a href="javascript:void(0);" onclick="getClose()" title="点击关闭播放" style="padding:10px;color:white;text-decoration:none;background:red;position:absolute;">关闭</a>';
            $("#play").html(link+kaiguan);
            $('#getpre').show();
        }
        function getEditPro(){ $("#editproduct1").show(); }
        function getThumb(){ $("#editthumb").show(); }
        function getLink(){ $("#editlink").show(); }
        function getDel(){ $("#del").show(); }
        function getClose(){ $(".editproduct").hide(); }
    </script>
@stop