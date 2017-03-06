@extends('admin.main')
@section('content')
    <div class="online_list">
        @include('admin.common.menu')

        <table class="list">
            <tr>
                <td><a href="javascript:;" style="color:lightgrey;"
                       onclick="window.location.href='{{DOMAIN}}admin/product/{{$product['id']}}';">返回上一页</a>
                </td>
                <td>产品名称：{{$product['name']}}</td>
                <td>产品类型：{{$product['cateName']}}</td>
            </tr>
            <tr><td colspan="5" style="border:0;">
                    <iframe id="modifytemp" width="985" height="450" frameborder=0 scrolling=no src="{{DOMAIN}}admin/pro/{{$product['id']}}/layer/{{$layerid}}"></iframe>
                    <iframe id="modifylayer" width="984" height="120" frameborder=0 scrolling=no src="{{DOMAIN}}admin/pro/{{$product['id']}}/{{$layerid}}/frame"></iframe>
                    <div id="buttonmenu" style="top:715px;">
                        <p>
                            <span style="color:#4d4d4d;"><b>层面板：</b></span>
                            <a href="javascript:;" id="editbg" title="修改模板大背景"
                                onclick="getEditBg()">产品背景</a>
                            {{--<a href="javascript:;" id="addlayer" title="添加新的动画设置"--}}
                                {{--onclick="getEditPro1()">添加动画</a>--}}
                            <span style="float:right;">
                                <a href="{{DOMAIN}}admin/pro/preview/{{$product['id']}}" target="_blank"
                                   title="预览整体动画效果">预览整体</a>
                                &nbsp; | &nbsp;
                                <a href="{{DOMAIN}}admin/pro/{{$product['id']}}/{{$layerid}}/prelayer"
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
    {{--<div class="editproduct" id="editproduct1">--}}
        {{--<div class="mask"></div>--}}
        {{--<form action="{{DOMAIN}}admin/pro/{{$product['id']}}/layer" method="POST" data-am-validator>--}}
            {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
            {{--<input type="hidden" name="_method" value="POST">--}}
            {{--<input type="hidden" name="pro_id" value="{{$product['id']}}">--}}
            {{--<p style="text-align:center"><b>{{$product['name']}} 添加动画</b></p>--}}
            {{--<p>动画片段名：--}}
                {{--<input type="text" minlength="2" maxlength="20" required name="name">--}}
            {{--</p>--}}
            {{--<p>开始时间点：--}}
                {{--<input type="text" pattern="^\d+$" required name="delay">--}}
            {{--</p>--}}
            {{--<p>时长：--}}
                {{--<input type="text" pattern="^\d+$" required name="timelong">--}}
            {{--</p>--}}
            {{--<p style="text-align:center">--}}
                {{--<input type="submit" id="submit" title="点击确定更新" value="确定添加">--}}
            {{--</p>--}}
            {{--<a href="javascript:void(0);" title="关闭" class="close" onclick="closeEditPro()"> X </a>--}}
        {{--</form>--}}
    {{--</div>--}}
    {{--弹出框：修改模板总背景--}}
    <div class="editproduct" id="editproduct2">
        <div class="mask"></div>
        <form action="{{DOMAIN}}admin/product/bg/{{$product['id']}}" method="POST" data-am-validator enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="pro_id" value="{{$product['id']}}">
            <p style="text-align:center"><b>{{$product['name']}} 修改背景</b></p>
            <p>类型选择：
                <select name="isbg" onchange="setBg(this.value)">
                    <option value="0" {{(!$proAttrArr||$proAttrArr['isbg']==0) ? 'selected ': ''}}>默认背景色</option>
                    <option value="1" {{($proAttrArr&&$proAttrArr['isbg']==1) ? 'selected ': ''}}>颜色更新</option>
                    <option value="2" {{($proAttrArr&&$proAttrArr['isbg']==2) ? 'selected ': ''}}>图片更新</option>
                </select>
            </p>
            <p id="selcolor" style="display:{{($proAttrArr&&$proAttrArr['isbg']==1) ? 'block' : 'none'}};">
                选择颜色：
                <input type="color" name="bgcolor" title="点击选择颜色" style="padding:0;height:40px;cursor:pointer;"
                       value="{{($proAttrArr&&$proAttrArr['bgcolor']) ? $proAttrArr['bgcolor'] : '#9a9a9a'}}">
            </p>
            <p id="selimg" style="display:{{($proAttrArr&&$proAttrArr['isbg']==2) ? 'block' : 'none'}};">
                @if($proAttrArr&&$proAttrArr['bgimg'])
                    <img src="{{$proAttrArr['bgimg']}}" width="300" height="150px"><br>
                    重新
                @endif
                选择图片：<br>
                <script src="{{PUB}}assets/js/local_pre.js"></script>
                <input type="button" class="submit" value="[ 找图 ]" onclick="path.click()"
                       style="width:90px;color:orangered;cursor:pointer;">
                <input type="file" id="path" style="display:none" name="url_ori"
                       onchange="url_file.value=this.value;loadImageFile();">
                <input type="text" placeholder="本地图片地址" name="url_file" readonly style="width:300px;">
                <div id="preview" style="width:300px;height:150px;border:1px dotted grey;
    filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale);display:none;"></div>
            </p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="closeEditPro()"> X </a>
        </form>
    </div>

    <script>
        function getLayer(layerid){
            var layerId0 = $("input[name='layerId0']").val();
            if (layerId0==layerid) {
                window.location.href = '{{DOMAIN}}admin/pro/{{$product['id']}}/layer';
            } else {
                window.location.href = '{{DOMAIN}}admin/pro/{{$product['id']}}/'+layerid+'/layer';
            }
        }
        function getEditPro1(){ $("#editproduct1").show(); }
        function closeEditPro(){ $('.editproduct').hide(); }
        function getEditBg(){ $("#editproduct2").show(); }
        function setBg(val){
            if (val==1) {
                $("#selcolor").show(); $("#selimg").hide(); $("#preview").hide();
            } else if (val==2) {
                $("#selimg").show(); $("#preview").show(); $("#selcolor").hide();
            } else if (val==0) {
                $("#selcolor").hide(); $("#preview").hide(); $("#selimg").hide();
            }

        }
    </script>
@stop