@extends('admin.main')
@section('content')
    <div class="online_list">
        @include('admin.common.menu')

        <table class="list">
            <tr>
                <td>模板名称：{{$data['name']}}</td>
                <td>模板编号：{{$data['serial']}}</td>
                <td>模板类型：{{$data['cateName']}}</td>
            </tr>
            <tr><td colspan="5">模板介绍：{{$data['intro']}}</td></tr>
            <tr><td colspan="5"><span style="float:right;">发布时间：{{$data['createTime']}}</span></td></tr>
            <tr><td colspan="5" style="border:0;">
                @if($data['thumb'])
                    <div id="toplay">
                        <a @if($data['link']) href="{{$data['link']}}" target="_blank" title="点击跳转到 {{$data['link']}}"
                           @else href="javascript:;" title="没有视频链接，需要添加" onclick="alert('没有视频链接！')"
                           @endif><img src="{{$data['thumb']}}"></a>
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
                    <a href="javascript:void(0);" onclick="getEditPro1()">修改模板</a>
                    <a href="javascript:void(0);" onclick="getThumb()">缩略图</a>
                    <a href="javascript:void(0);" onclick="getLink()">视频链接</a>
                    <a href="@if($data['linkType']==4){{$data['link']}}@else javascript:void(0);@endif"
                       @if($data['linkType']==4) target="_blank" @else onclick="getPlay()" @endif>播放</a>
                    <a href="{{DOMAIN}}admin/t/{{$data['id']}}/layer">细节修改</a>
                    <span id="isshow">
                        @if($data['isshow']==2)
                        <a href="javascript:;" title="设置前台模板隐藏" onclick="setShow(1)">前台隐藏</a>
                        @else
                        <a href="javascript:;" title="设置前台模板显示" onclick="setShow(2)">前台显示</a>
                        @endif
                    </span>
                    @if(Session::get('admin.username')=='jiuge')
                    <a href="javascript:void(0);" title="彻底删除模板" onclick="getDel()">删除模板</a>
                    @endif
                </div>
            </td></tr>
        </table>
    </div>

    <input type="hidden" name="linkType" value="{{$data['linkType']}}">
    <input type="hidden" name="link" value="{{$data['link']}}">
    <input type="hidden" name="id" value="{{$data['id']}}">
    <input type="hidden" name="uid" value="{{Session::has('user')?Session::get('user.uid'):0}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="tempid" value="{{$data['id']}}">
    {{--弹出框：修改产品数据--}}
    <div class="editproduct" id="editproduct1">
        <div class="mask"></div>
        <form action="{{DOMAIN}}admin/temp/{{$data['id']}}" method="POST" data-am-validator>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <p style="text-align:center"><b>{{$data['name']}} 产品修改</b></p>
            <p>产品名称：
                <input type="text" minlength="2" maxlength="20" required name="name" value="{{$data['name']}}">
            </p>
            <p>产品类型：
                <select name="cate" required>
                    @foreach($model['cates'] as $kcate=>$vcate)
                        <option value="{{$kcate}}" {{$data['cate']==$kcate?'selected':''}}>{{$vcate}}</option>
                    @endforeach
                </select>
            </p>
            <p>产品介绍：
                <textarea rows="5" style="resize:none;" name="intro">{{$data['intro']}}</textarea>
            </p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="closePro()"> X </a>
        </form>
    </div>
    {{--弹出框：添加缩略图--}}
    <div class="editproduct" id="editthumb">
        <div class="mask"></div>
        <form action="{{DOMAIN}}admin/temp/link1/{{$data['id']}}" method="POST"
              data-am-validator enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <p style="text-align:center"><b>{{$data['name']}} 缩略图修改</b></p>
            <p>缩略图更新：<br>
                @include('layout.uploadimg')
            </p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="closePro()"> X </a>
        </form>
    </div>
    {{--弹出框：视频链接--}}
    <div class="editproduct" id="editlink">
        <div class="mask"></div>
        <form action="{{DOMAIN}}admin/temp/link2/{{$data['id']}}" method="POST"
              data-am-validator enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
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
            <a href="javascript:void(0);" title="关闭" class="close" onclick="closePro()"> X </a>
        </form>
    </div>
    {{--弹出框：删除产品--}}
    <div class="editproduct" id="editproduct3">
        <div class="mask"></div>
        <div class="msg">
            <p style="text-align:center;">
                确定要彻底删除 <b>{{$data['name']}}</b> 信息么？ <br><br>
                <a href="{{DOMAIN}}u/product/delete/{{$data['id']}}">确定删除</a>
                <a href="javascript:void(0);" onclick="closePro()">取消</a>
            </p>
        </div>
    </div>

    <script>
        function getReturnPre(){ window.location.href = '{{DOMAIN}}admin'; }
        function getPlay(){
            var linkType = $("input[name='linkType']").val();
            var link = $("input[name='link']").val();
            if (link=='') {
                alert('没有视频链接！');return;
            } else if (linkType!=4) {
                $("#toplay").html(link);
            } else {
                window.location.href = link;
            }
        }
        function getEditPro1(){ $("#editproduct1").show(); }
        function getThumb(){ $("#editthumb").show(); }
        function getLink(){ $("#editlink").show(); }
        function closePro(){ $(".editproduct").hide(); }
        function getDel(){ $("#editproduct3").show(); }
        function setShow(isshow){
            $.ajaxSetup({headers : {'X-CSRF-TOKEN':$('input[name="_token"]').val()}});
            var tempid = $("input[name='tempid']").val();
            $.ajax({
                type: 'POST',
                url: '{{DOMAIN}}admin/temp/getshow/'+tempid,
                data: {'id':tempid,'isshow':isshow},
                dataType: 'json',
                success: function(data) {
                    if (data.code!=0) {
                        alert(data.msg);return;
                    }
                    var html = '';
                    if (isshow==1) {
                        html = '<a href="javascript:;" title="设置前台模板显示" onclick="setShow(2)">前台显示</a>';
                    } else {
                        html = '<a href="javascript:;" title="设置前台模板隐藏" onclick="setShow(1)">前台隐藏</a>';
                    }
                    $("#isshow").html(html);
                }
            });
        }
    </script>
@stop