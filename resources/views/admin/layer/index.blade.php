@extends('admin.main')
@section('content')
    <div class="online_list">
        @include('admin.common.menu')

        <table class="list">
            <tr>
                <td><a href="javascript:;" style="color:lightgrey;"
                       onclick="window.location.href='{{DOMAIN}}admin/temp/{{$temp['id']}}';">返回上一页</a>
                </td>
                <td>产品编号：{{$temp['serial']}}</td>
                <td>产品名称：{{$temp['name']}}</td>
                <td>产品类型：{{$temp['cateName']}}</td>
            </tr>
            <tr><td colspan="5" style="border:0;">
                    <iframe id="modifytemp" width="980" height="450" frameborder=0 scrolling=no src="{{DOMAIN}}admin/t/{{$temp['id']}}/layer/{{$layerid}}"></iframe>
                    <iframe id="modifylayer" width="984" height="100" frameborder=0 scrolling=no src="{{DOMAIN}}admin/t/{{$temp['id']}}/{{$layerid}}/frame"></iframe>
                    <div id="buttonmenu" style="top:715px;">
                        <p>
                            <span style="color:#4d4d4d;"><b>层面板：</b></span>
                            <a href="javascript:;" id="addlayer" title="添加新的动画设置"
                                onclick="getEditPro1()">添加动画</a>
                            <span style="float:right;">
                                <a href="{{DOMAIN}}admin/temp/preview/{{$temp['id']}}" target="_blank"
                                   title="预览整体动画效果">预览整体</a>
                                &nbsp; | &nbsp;
                                <a href="{{DOMAIN}}admin/t/{{$temp['id']}}/{{$layerid}}/prelayer"
                                   title="预览当前片段[ {{$layerName}} ]"
                                   target="_blank" id="preCurrLayer">
                                    预览[{{str_limit($layerName,8)}}]</a> &nbsp;
                            </span>
                        </p>
                        @if(!count($datas))
                            <p style="text-align:center;">没有动画</p>
                        @else
                            @foreach($datas as $data)
                                <div class="timetab">
                                    <a href="javascript:;" title="{{$data['name']}}"
                                       class="{{$data['id']==$layerid?'curr':''}} atab" id="atab_{{$data['id']}}"
                                       onclick="getLayer({{$data['id']}})">{{str_limit($data['name'],8)}}
                                        ({{$data['timelong']}}s)</a>
                                    <input type="hidden" name="layerName_{{$data['id']}}" value="{{$data['name']}}">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </td></tr>
        </table>
    </div>
    <input type="hidden" name="layerId0" value="{{count($datas)?$datas[0]['id']:0}}">
    <input type="hidden" name="layerName" value="{{count($datas)?$datas[0]['name']:''}}">


    {{--弹出框：添加动画--}}
    <div class="editproduct" id="editproduct1">
        <div class="mask"></div>
        <form action="{{DOMAIN}}admin/t/{{$temp['id']}}/layer" method="POST" data-am-validator>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="tempid" value="{{$temp['id']}}">
            <p style="text-align:center"><b>{{$temp['name']}} 添加动画</b></p>
            <p>动画片段名：
                <input type="text" minlength="2" maxlength="20" required name="name">
            </p>
            <p>开始时间点：
                <input type="text" pattern="^\d+$" required name="delay">
            </p>
            <p>时长：
                <input type="text" pattern="^\d+$" required name="timelong">
            </p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="closeEditPro1()"> X </a>
        </form>
    </div>
    <script>
        function getLayer(layerid){
            var layerId0 = $("input[name='layerId0']").val();
            if (layerId0==layerid) {
                window.location.href = '{{DOMAIN}}admin/t/{{$temp['id']}}/layer';
            } else {
                window.location.href = '{{DOMAIN}}admin/t/{{$temp['id']}}/'+layerid+'/layer';
            }
            /*$(".atab").removeClass("curr");
            $("#atab_"+layerid).addClass("curr");
            $("#modifytemp")[0].src = "{{DOMAIN}}admin/t/{{$temp['id']}}/layer/"+layerid;
            $("#modifylayer")[0].src = "{{DOMAIN}}admin/t/{{$temp['id']}}/"+layerid+"/frame";
            //更新当前动画名称
            var layerNameCurr = $("input[name='layerName_"+layerid+"']").val();
            $("input[name='layerName']")[0].value = layerNameCurr;
            var layerName = $("input[name='layerName']").val();
            var preCurrLayer = $("#preCurrLayer");
            var layerNameLimit;     //限制字数
            if (layerName.length>4) {
                layerNameLimit = layerName.substring(0,4)+'...';
            } else {
                layerNameLimit = layerName;
            }
            preCurrLayer[0].innerText = "预览["+layerNameLimit+"]";
            preCurrLayer[0].title = '预览当前动画片段[ '+layerName+' ]';
            preCurrLayer[0].href = '{{DOMAIN}}admin/t/{{$temp['id']}}/'+layerid+'/prelayer';*/
        }
        function getEditPro1(){ $("#editproduct1").show(); }
        function closeEditPro1(){ $("#editproduct1").hide(); }
    </script>
@stop