@extends('admin.main')
@section('content')
    <style>
        #preview { margin:10px 100px; width:800px; height:450px; border:5px solid #333; box-shadow:0 0 10px black; }
        .layer { margin:10px;margin-left:0;float:left; }
        .layer a { padding:2px 10px;border:1px solid grey;float:left; }
        .layer a.caozuo { padding:3px 5px; border:1px solid darkred; border-left:0; font-size:14px; float:left; }
        /*弹出框*/
        #popup { display:none; }
        #popup .mask { width:100%;height:100%; background:black; position:fixed;left:0;top:0;
            filter:alpha(opacity=50); /*IE滤镜，透明度50%*/
            -moz-opacity:0.5; /*Firefox私有，透明度50%*/
            opacity:0.5;/*其他，透明度50%*/
        }
        #popup .content { margin:250px auto; padding:10px; width:500px; background:white; position:fixed;left:30%;top:0; }
        #popup p { font-size:20px;text-align:center; }
        #popup a { margin:0 5px;padding:2px 20px;color:lightgrey;text-decoration:none;background:orangered; }
        #popup a:hover { color:white; }
    </style>

    <div class="online_list">
        @include('admin.common.menu')
        <table class="list">
            <tr><td colspan="5">
                    <a href="javascript:;" style="color:lightgrey;"
                       onclick="window.location.href='{{DOMAIN}}admin/t/{{$temp['id']}}/layer';">返回上一页</a>
                </td></tr>
            <tr>
                <td>模板名称：{{$temp['name']}}</td>
                <td>模板编号：{{$temp['serial']}}</td>
                <td>动画数量：{{$temp['layerNum']}}</td>
                <td>创建时间：{{$temp['createTime']}}</td>
                <td><a href="">刷新</a></td>
            </tr>
            <tr>
                <td colspan="5">
                    <div class="layer">所有动画：</div>
                    @if($layers)
                    @foreach($layers as $layer)
                        <div class="layer">
                            <a href="javascript:;">{{$layer['id'].'-'.$layer['name']}}</a>
                            <a href="{{DOMAIN}}admin/t/{{$temp['id']}}/{{$layer['id']}}/layer/setshow/{{$layer['isshow']==1?2:1}}"
                               class="caozuo" title="设置该动画层显示或隐藏">{{$layer['isshow']==1?'显示':'隐藏'}}</a>
                            <a href="javascript:;" class="caozuo" title="彻底删除该动画层"
                               onclick="delLayer({{$layer['id']}})">删除</a>
                            <input type="hidden" name="layerName_{{$layer['id']}}" value="{{$layer['name']}}">
                        </div>
                    @endforeach
                    @endif
                </td>
            </tr>
            <tr><td colspan="5" style="border:0;">
                    <iframe id="preview" frameborder=0 scrolling=no src="{{DOMAIN}}admin/temp/preview/layers/{{$temp['id']}}"></iframe>
                </td>
            </tr>
        </table>
    </div>

    <div id="popup">
        <div class="mask"></div>
        <div class="content">
            <p><b id="del_title">彻底删除动画</b></p>
            <p>
                <a href="javascript:;" id="del_sure">确定</a>
                <a href="javascript:;" onclick="$('#popup').hide(100);">取消</a>
            </p>
        </div>
    </div>

    <script>
        function delLayer(layerid){
            var layerName = $("input[name='layerName_"+layerid+"']").val();
            $("#del_title").html("彻底删除动画 - "+layerName);
            $("#del_sure")[0].href = '{{DOMAIN}}admin/t/{{$temp['id']}}/'+layerid+'/layer/delete';
            $("#popup").show(100);
        }
    </script>
@stop