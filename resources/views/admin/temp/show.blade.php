@extends('admin.main')
@section('content')
    <div class="online_list">
        @include('admin.common.menu')

        <table class="list">
            <tr><td colspan="2">产品编号：{{$data['serial']}}</td></tr>
            <tr>
                <td>产品名称：{{$data['name']}}</td>
                <td>产品类型：{{$data['cateName']}}</td>
            </tr>
            <tr><td colspan="2">产品介绍：{{$data['intro']}}</td></tr>
            <tr><td colspan="2"><span style="float:right;">发布时间：{{$data['createTime']}}</span></td></tr>
            <tr><td colspan="2" style="border:0;">
                @if($data['link'])
                    <div id="toplay"><img src="{{$data['thumb']}}"></div>
                @else
                    <div id="toplay" title="没有缩略图或视频链接，等待添加">
                        <p>没有缩略图或视频，等待添加...
                            <a href="javascript:void(0);" style="color:orangered;" onclick="getEditPro2()">马上添加</a>
                        </p>
                    </div>
                @endif
                    <input type="hidden" name="linkType" value="{{$data['linkType']}}">
                    <input type="hidden" name="link" value="{{$data['link']}}">
                    <input type="hidden" name="id" value="{{$data['id']}}">
                    <input type="hidden" name="uid" value="{{Session::has('user')?Session::get('user.uid'):0}}">
                    <div id="editpro">
                        <a href="javascript:void(0);" onclick="getEditPro1()">修改产品</a>
                        @if($data['thumb'] || $data['link'])
                        <a href="javascript:void(0);" onclick="getEditPro2()">缩略图/视频链接修改</a>
                        @endif
                        <a href="@if($data['linkType']==4){{$data['link']}}@else javascript:void(0);@endif"
                           @if($data['linkType']==4)target="_blank"@elseonclick="getPlay()"@endif>播放</a>
                        <a href="{{DOMAIN}}admin/temp/preview/{{$data['id']}}">预览动画</a>
                        <a href="{{DOMAIN}}admin/t/{{$data['id']}}/layer">修改动画</a>
                        <a href="javascript:void(0);" onclick="getDel()">删除产品</a>
                    </div>
                </td></tr>
        </table>
    </div>
    {{--弹出框：添加缩略图、视频链接--}}
    <div class="editproduct" id="editproduct2">
        <div class="mask"></div>
        <form action="{{DOMAIN}}admin/temp/link/{{$data['id']}}" method="POST"
              data-am-validator enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <p style="text-align:center"><b>{{$data['name']}} 链接修改</b></p>
            <p>缩略图：<br>@include('layout.uploadimg')</p>
            <p>视频链接类型：
                <select name="linkType" required>
                    @foreach($model['linkTypes'] as $klinkType=>$vlinkType)
                        <option value="{{$klinkType}}" {{$data['linkType']==$klinkType?'selected':''}}>
                            {{$vlinkType}}</option>
                    @endforeach
                </select>
            </p>
            <p>视频链接：
                <input type="text" placeholder="例：" minlength="2" maxlength="20" required
                       name="link" value="{{$data['link']}}">
            </p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="closeEditPro2()"> X </a>
        </form>
    </div>
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
            <a href="javascript:void(0);" title="关闭" class="close" onclick="closeEditPro1()"> X </a>
        </form>
    </div>
    {{--弹出框：删除产品--}}
    <div class="editproduct" id="editproduct3">
        <div class="mask"></div>
        <div class="msg">
            <p style="text-align:center;">
                确定要删除 <b>{{$data['name']}}</b> 信息么？ <br><br>
                <a href="{{DOMAIN}}u/product/delete/{{$data['id']}}">确定删除</a>
                <a href="javascript:void(0);" onclick="closeEditPro3()">取消</a>
            </p>
        </div>
    </div>

    <script>
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
        function closeEditPro1(){
//            $("#editproduct1").hide();
            window.location.href = '';
        }
        function getEditPro2(){ $("#editproduct2").show(); }
        function closeEditPro2(){
//            $("#editproduct2").hide();
            window.location.href = '';
        }
        function getDel(){ $("#editproduct3").show(); }
        function closeEditPro3(){
//            $("#editproduct3").hide();
            window.location.href = '';
        }
    </script>
@stop