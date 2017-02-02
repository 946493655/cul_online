<script src="{{PUB}}assets/js/jquery-1.10.2.min.js"></script>
<style>
    body { margin:0;padding:0;font-family:'微软雅黑'; }
    #keys { padding:0 5px;width:980px;height:130px;background:black; }
    #keys #title { margin:0;padding:0 5px;color:#4d4d4d;border-bottom:1px dashed #333;font-size:16px; }
    #keyframe { width:1000px;height:130px;overflow:scroll; }
    #keyframe .left {
        width:75px;
        @if(isset($frames)&&count($frames)<4)height:100px;@endif
         border-right:2px dashed #333;
    }
    #keyframe .left p { margin:0;margin-top:2px; }
    #keyframe a { color:grey;text-decoration:none; }
    #keyframe a:hover,#keyframe a.curr { color:orangered; }
    #keyframe .right { width:900px;position:absolute;top:20px;left:90px; }
    #keyframe .right .dui { margin:2px;float:left; }
    #keyframe .right .dui span { font-size:12px;color:grey; }
    #keyframe .right .dui input[type="button"] {
        color:grey;border:1px solid #2a2a2a;background:#2a2a2a;outline:none;cursor:pointer; }
    #keyframe .right .dui input[type="button"]:hover { color:white;border:1px solid orangered;background:#333; }
    #keyframe .right input {
        width:25px;border:0;color:orangered;text-align:center;font-size:14px;background:#333;
    }
    /*添加关键帧*/
    #addkey { display:none; }
    #addkey form {
        padding:0 15px;width:1000px;height:120px;color:grey;background:white;position:fixed;left:0;top:0;
    }
    #addkey p { text-align:center; }
    #addkey input,#addkey select {
        padding:5px;width:100px;border:1px solid lightgrey;
    }
    #addkey input[type="submit"],#addkey input[type="button"] {
        padding:5px 0;width:80px;color:white;font-weight:bold;border:0;cursor:pointer;
    }
    #addkey input[type="submit"] { background:#4d4d4d; }
    #addkey input[type="button"] { background:orangered; }
</style>

<div id="keys">
    <div id="title">
        <b>动画调节：</b>
        <span style="font-size:14px">(鼠标滚动 | 下面是 [左边时间-右边值] 的组合：时间=百分比x时长)</span>
        <span style="float:right;" id="titlebtn">
            <a href="javascript:;" title="增加运动轨迹" style="color:orangered;" onclick="$('#addkey').show(200);">添加关键帧</a>
            @if($frameRedis)
            &nbsp; | &nbsp;
            <a href="javascript:;" title="不保存修改" style="color:orangered;" onclick="cancel()">取消更新</a>&nbsp;
            <a href="javascript:;" title="保存修改" style="color:orangered;" onclick="save()">确定更新</a>
            @endif
        </span>
    </div>
    <div id="keyframe">
        <div class="left">
            <p>
                <a href="javascript:;" title="点击修改平移动画" class="{{$attr==1?'curr':''}} aleft" id="left_1"
                   onclick="selAttr(1)">水平距离</a>
            </p>
            <p>
                <a href="javascript:;" title="点击修改垂直动画" class="{{$attr==2?'curr':''}} aleft" id="left_2"
                   onclick="selAttr(2)">垂直距离</a>
            </p>
            <p>
                <a href="javascript:;" title="点击修改透明度动画" class="{{$attr==3?'curr':''}} aleft" id="left_3"
                   onclick="selAttr(3)">透明度</a>
            </p>
            <div style="height:20px;"></div>
        </div>
        <div class="right">
            <div class="dui" id="dui_1" style="display:{{$attr==1?'block':'none'}};">
            @if($leftArr)
                @foreach($leftArr as $left)
                <input type="text" name="per_{{$left['id']}}" value="{{$left['per']}}" title="修改该百分比"
                       onchange="setFrame({{$left['id']}})">
                <span>%-</span>
                <input type="text" name="val_{{$left['id']}}" value="{{$left['val']}}" title="修改该值"
                       onchange="setFrame({{$left['id']}})">
                <input type="button" class="close" title="点击删除该关键帧" value="X" onclick="delFrame({{$left['id']}})">
                <span>，</span> &nbsp;
                @endforeach
            @else <span style="font-size:16px">没有水平距离关键帧</span>
            @endif
            </div>
            <div class="dui" id="dui_2" style="display:{{$attr==2?'block':'none'}};">
            @if($topArr)
                @foreach($topArr as $top)
                <input type="text" name="per_{{$top['id']}}" value="{{$top['per']}}" title="修改该百分比"
                       onchange="setFrame({{$top['id']}})">
                <span>%-</span>
                <input type="text" name="val_{{$top['id']}}" value="{{$top['val']}}" title="修改该值"
                       onchange="setFrame({{$top['id']}})">
                <span>，</span> &nbsp;
                @endforeach
            @else <span style="font-size:16px">没有垂直距离关键帧</span>
            @endif
            </div>
            <div class="dui" id="dui_3" style="display:{{$attr==3?'block':'none'}};">
            @if($opacityArr)
                @foreach($opacityArr as $opacity)
                <input type="text" name="per_{{$opacity['id']}}" value="{{$opacity['per']}}" title="修改该百分比"
                       onchange="setFrame({{$opacity['id']}})">
                <span>%-</span>
                <input type="text" name="val_{{$opacity['id']}}" value="{{$opacity['val']}}" title="修改该值"
                       onchange="setFrame({{$opacity['id']}})">
                <span>，</span> &nbsp;
                @endforeach
            @else <span style="font-size:16px">没有透明度关键帧</span>
            @endif
            </div>
        </div>
        <div style="height:10px;"></div>
        <input type="hidden" name="attr" value="{{$attr}}">
    </div>
</div>

<div id="addkey">
    <form action="{{DOMAIN}}admin/t/{{$tempid}}/{{$layerid}}/frame" method="POST" data-am-validator>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="tempid" value="{{$tempid}}">
        <input type="hidden" name="layerid" value="{{$layerid}}">
        <p><b>添加关键帧</b></p>
        <p>
            选择属性：
            <select name="selattr">
                @foreach($model['attrNames'] as $k=>$attrName)
                    <option value="{{$k}}">{{$attrName}}</option>
                @endforeach
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;
            时间百分比：
            <input type="text" pattern="^\d{0,3}$" maxlength="100" name="per"> %
            &nbsp;&nbsp;&nbsp;&nbsp;
            值：
            <input type="text" name="val">
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" title="点击确定更新" value="确定添加">
            <input type="button" title="点击取消" value="取消" onclick="$('#addkey').hide(200);">
        </p>
    </form>
</div>

<script>
    $.ajaxSetup({headers : {'X-CSRF-TOKEN':$('input[name="_token"]').val()}});
    var tempid = $("input[name='tempid']").val();
    var layerid = $("input[name='layerid']").val();
    function selAttr(attr){
        $(".aleft").removeClass("curr"); $("#left_"+attr).addClass("curr");
        $(".dui").hide(); $("#dui_"+attr).show();
        var data = {
            'tempid':tempid,
            'layerid':layerid,
            'attr':attr
        };
        $.ajax({
            type: 'POST',
            url: '/admin/t/'+tempid+'/'+layerid+'/frame/toattr',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.code!=0) {
                    alert(data.msg);return;
                }
            }
        });
    }
    function setFrame(frameid){
        var attr = $("input[name='attr']").val();
        var per = $("input[name='per_"+frameid+"']").val();
        var val = $("input[name='val_"+frameid+"']").val();
        var data = {
            'layerid':layerid,
            'frameid':frameid,
            'attr':attr,
            'per':per,
            'val':val
        };
        $.ajax({
            type: 'POST',
            url: '/admin/t/'+tempid+'/'+layerid+'/frame/setval',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.code!=0) {
                    alert(data.msg);return;
                } else {
                    var html = '<a href="javascript:;" title="增加运动轨迹" style="color:orangered;" onclick="$(\'#addkey\').show(200);">添加关键帧</a> ';
                    html += '@if($frameRedis)';
                    html += '&nbsp; | &nbsp; <a href="javascript:;" title="不保存修改" style="color:orangered;" onclick="cancel()">取消更新</a> &nbsp;<a href="javascript:;" title="保存修改" style="color:orangered;" onclick="save()">确定更新</a>';
                    html += '@endif';
                    $("#titlebtn").html(html);
                }
            }
        });
    }
    function cancel(){
        window.location.href = '{{DOMAIN}}admin/t/'+tempid+'/'+layerid+'/frame/cancel';
    }
    function save(){
        window.location.href = '{{DOMAIN}}admin/t/'+tempid+'/'+layerid+'/frame/save';
    }
    function delFrame(frameid){
        var attr = $("input[name='attr']").val();
        $.ajax({
            type: 'POST',
            url: '/admin/t/'+tempid+'/'+layerid+'/frame/delete',
            data: {'id':frameid,'layerid':layerid,'attr':attr},
            dataType: 'json',
            success: function(data) {
                if (data.code!=0) {
                    alert(data.msg);return;
                } else {
                    window.location.href = '';
                }
            }
        });
    }
</script>