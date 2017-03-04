<style>
    body { margin:0;padding:0;font-family:'微软雅黑'; }
    /*===div的滚动条修改===*/
    #menu { margin-top:5px;padding:2px;padding-bottom:20px;width:175px;height:450px;color:grey;background:black;
        border-left:1px solid grey;overflow:scroll;position:absolute;top:-10px;left:800px; }
    /*#menu ::-webkit-scrollbar-corner { background:transparent; }*/
    #menu::-webkit-scrollbar { width:5px; }
    #menu::-webkit-scrollbar-track{ background-color:#1a1a1a; }
    #menu::-webkit-scrollbar-thumb{ background-color:#4a4a4a; }
    #menu::-webkit-scrollbar-thumb:hover { background-color:#6a6a6a; }
    #menu::-webkit-scrollbar-thumb:active { background-color:#6a6a6a; }
    /*===input的color默认颜色更改===*/
    input[type="color"] { padding:0;height:20px;border:0;cursor:pointer;position:relative;top:6px; }
    input[type="color"]::-webkit-color-swatch { padding:0 25px;height:15px;border:2px solid grey;position:relative;top:-6px;left:-7px; }

    /*右侧菜单面板*/
    #menu #title { width:180px;color:#4d4d4d;position:fixed;top:0px;z-index:10;background:black; }
    #menu .menu { margin:0;padding:5px 0;padding-left:10px;border-bottom:1px dashed #212121; }
    #menu a { color:grey;text-decoration:none; }
    #menu a:hover { color:orangered; }
    #menu .menutab { padding:5px;padding-left:10px;color:grey;display:none; }
    #menu .menutab p { margin:0;padding:0; }
    #menu input,#menu select { margin:2px 0;padding:2px;width:25px;border:0;color:orangered;font-size:16px;background:#333; }
    #menu select { width:70px; }
    #menu input.radio { margin:0;padding:0;width:10px; }
    #menu textarea { margin:5px 0;padding:5px;width:140px;height:100px;border:1px solid #333;border-radius:3px;color:orangered;background:#333;resize:none; }
    #menu input[type="submit"] { width:80px;border:1px solid #333; }
    #menu input[type="submit"]:hover { border:1px solid #660000; }
    #submit { padding:5px 10px;width:170px;background:#1a1a1a;position:fixed;top:420px;left:800px;z-index:10; }
    #submit .submit { padding:6px 10px;font-size:16px;color:lightgrey;cursor:pointer; }

    /*动画窗口*/
    #tempbg {
        width:800px; height:450px;overflow:hidden;
        @if(!isset($proAttrArr['isbg'])||(isset($proAttrArr['isbg'])&&!$proAttrArr['isbg'])){{'background:#9a9a9a;'}}
        @elseif(isset($proAttrArr['isbg'])&&$proAttrArr['isbg']==1){{'background:'.$proAttrArr['bgcolor'].';'}}
        @endif
    }
    #iframe {
        width:800px; height:450px;
        @if(isset($attrs['isbigbg'])&&$attrs['isbigbg']) background:{{$attrs['bigbg']}};@endif
        position:absolute;top:0;left:0;
        overflow:hidden;
    }
    #iframe #attr_default {
        margin:100px auto; padding:5px;
        width:300px; height:100px;
        color:grey; background:white;
        overflow:hidden;
    }
    #iframe #attr {
        margin:100px auto;padding:5px;
        overflow:hidden;
        /*transform:rotate(45deg);*/
    @if($attrs)
        width:{{$attrs['width']?$attrs['width']:'300'}}px;
        height:{{$attrs['height']?$attrs['height']:'100'}}px;
        @if($attrs['isborder']) border:{{$attrs['border1'].'px '.$model['border2s'][$attrs['border2']].' '.$attrs['border3']}}; @endif
        @if($attrs['isbg']) background:{{$attrs['bg']}}; @endif
        @if($attrs['iscolor']) color:{{$attrs['color']}}; @endif
        @if($attrs['fontsize']) font-size:{{$attrs['fontsize'].'px'}}; @endif
    @endif
    }
</style>


{{--动画窗口--}}
<div id="tempbg">{{--模板大背景--}}
    @if(isset($proAttrArr['isbg'])&&$proAttrArr['isbg']==2)
        <img src="{{$proAttrArr['bgimg']}}">
    @endif
</div>
<div id="iframe">
@if(!$layers)
    <div id="{{$attrs?'attr':'attr_default'}}">没有记录...</div>
@else
    <div id="{{$attrs?'attr':'attr_default'}}">
        @if($cons['text'] && $cons['iscon']==1) {{$cons['text']}}
        @elseif($cons['img'] && $cons['iscon']==2) <img src="{{$cons['img']}}">
        @else 文字待输入...
        @endif
    </div>
@endif
</div>


{{--菜单面板--}}
<div id="menu">
    <div class="menu" id="title"><b>属性菜单：</b><span style="font-size:12px">(鼠标滚动)</span></div>
    <div style="height:35px;"></div>
@if($layers)
    {{--下面是菜单--}}
    <div class="menu"><a href="javascript:;" title="点击切换" onclick="getMenu(1)">动画设置：</a>
        <div class="menutab" id="menu1" style="display:{{((isset($menutab)&&$menutab==1)||!isset($menutab))?'block':'none'}};">
            名称：<input type="text" minlength="2" maxlength="20" required style="width:90px" name="name" value="{{$layers['name']}}" onchange="setLayer()">
            <br>
            起始时间：<input type="text" pattern="^\d+$" required name="delay" value="{{$layers['delay']}}" onchange="setLayer()"> s
            <br>
            时 长：<input type="text" pattern="^[1-9]+$" required name="timelong" value="{{$layers['timelong']}}" onchange="setLayer()"> s
        </div>
    </div>
    <div class="menu"><a href="javascript:;" title="点击切换" onclick="getMenu(2)">样式属性：</a>
        <div class="menutab" id="menu2" style="display:{{(isset($menutab)&&$menutab==2)?'block':'none'}};">
            <p>大背景：<label><input type="radio" class="radio" name="isbigbg0" value="0" @if(isset($attrs['isbigbg'])){{!$attrs['isbigbg']?'checked':''}}@else{{'checked'}}@endif onclick="isbigbg(0)" onchange="setAttr()">无 </label>
                <label><input type="radio" class="radio" name="isbigbg1" value="1" @if(isset($attrs['isbigbg'])){{$attrs['isbigbg']?'checked':''}}@endif onclick="isbigbg(1)" onchange="setAttr()">有 </label></p>
            <div id="isbigbg" style="display:{{((isset($attrs['isbigbg'])&&$attrs['isbigbg'])||!isset($attrs['isbigbg']))?'block':'none'}};">
                <input type="hidden" name="isbigbg" value="{{(isset($attrs['isbigbg'])&&$attrs['isbigbg'])?$attrs['isbigbg']:0}}">
                <p style="position:relative;top:-6px;">&nbsp;&nbsp;
                    选颜色：<input type="color" title="点击选择颜色" value="{{(isset($attrs['bigbg'])&&$attrs['bigbg'])?$attrs['bigbg']:'#9a9a9a'}}" onchange="setBigBgColor(this.value)">
                    <input type="hidden" name="bigbg" value="{{(isset($attrs['bigbg'])&&$attrs['bigbg'])?$attrs['bigbg']:'#9a9a9a'}}">
                </p>
            </div>

            <p>宽度：<input type="text" name="width" style="width:50px;" placeholder="300" value="{{(isset($attrs['width'])&&$attrs['width'])?$attrs['width']:''}}" onchange="setAttr()"> px</p>

            <p>高度：<input type="text" name="height" style="width:50px;" placeholder="100" value="{{(isset($attrs['height'])&&$attrs['height'])?$attrs['height']:''}}" onchange="setAttr()"> px</p>

            <p>边框： <label><input type="radio" class="radio" name="isborder0" value="0" @if(isset($attrs['isborder'])){{!$attrs['isborder']?'checked':''}}@else{{'checked'}}@endif onclick="isborder(0)" onchange="setAttr()">无 </label>
            <label><input type="radio" class="radio" name="isborder1" value="1" @if(isset($attrs['isborder'])){{$attrs['isborder']?'checked':''}}@endif onclick="isborder(1)" onchange="setAttr()">有 </label></p>
            <div id="isborder" style="display:{{(isset($attrs['isborder'])&&$attrs['isborder'])?'block':'none'}};">
                <input type="hidden" name="isborder" value="{{(isset($attrs['isborder'])&&$attrs['isborder'])?$attrs['isborder']:0}}">
                <p>&nbsp;&nbsp;
                    <input type="text" required name="border1" value="{{(isset($attrs['border1'])&&$attrs['border1'])?$attrs['border1']:1}}" onchange="setAttr()"> px,
                    <select name="border2" onchange="setAttr()">
                        @foreach($model['border2names'] as $kborder2=>$vborder2)
                            <option value="{{$kborder2}}" {{(isset($attrs['border2'])&&$attrs['border2']==$kborder2)?'selected':''}}>{{$vborder2}}</option>
                        @endforeach
                    </select>
                </p>
                <p style="position:relative;top:-6px;">&nbsp;&nbsp;
                    选颜色：<input type="color" title="点击选择颜色" value="{{(isset($attrs['border3'])&&$attrs['border3'])?$attrs['border3']:'#ff0000'}}" onchange="setBoderColor(this.value)">
                    <input type="hidden" name="border3" value="{{(isset($attrs['border3'])&&$attrs['border3'])?$attrs['border3']:'#ff0000'}}">
                </p>
            </div>

            <p>背景色：<label><input type="radio" class="radio" name="isbg0" value="0" @if(isset($attrs['isbg'])){{!$attrs['isbg']?'checked':''}}@endif onclick="isbg(0)" onchange="setAttr()">无 </label>
            <label><input type="radio" class="radio" name="isbg1" value="1" @if(isset($attrs['isbg'])){{$attrs['isbg']?'checked':''}}@else{{'checked'}}@endif onclick="isbg(1)" onchange="setAttr()">有 </label></p>
            <div id="isbg" style="display:{{((isset($attrs['isbg'])&&$attrs['isbg'])||!isset($attrs['isbg']))?'block':'none'}};">
                <input type="hidden" name="isbg" value="{{(isset($attrs['isbg'])&&!$attrs['isbg'])?$attrs['isbg']:1}}">
                <p style="position:relative;top:-6px;">&nbsp;&nbsp;
                    选颜色：<input type="color" title="点击选择颜色" value="{{(isset($attrs['bg'])&&$attrs['bg'])?$attrs['bg']:'#ffffff'}}" onchange="setBgColor(this.value)">
                    <input type="hidden" name="bg" value="{{(isset($attrs['bg'])&&$attrs['bg'])?$attrs['bg']:'#ffffff'}}">
                </p>
            </div>

            <p>字颜色：<label><input type="radio" class="radio" name="iscolor0" value="0" @if(isset($attrs['iscolor'])){{!$attrs['iscolor']?'checked':''}}@else{{'checked'}}@endif onclick="iscolor(0)" onchange="setAttr()">默认 </label>
            <label><input type="radio" class="radio" name="iscolor1" value="1" @if(isset($attrs['iscolor'])){{$attrs['iscolor']?'checked':''}}@endif onclick="iscolor(1)" onchange="setAttr()">有 </label></p>
            <div id="iscolor" style="display:{{(isset($attrs['iscolor'])&&$attrs['iscolor'])?'block':'none'}};">
                <input type="hidden" name="iscolor" value="{{(isset($attrs['iscolor'])&&$attrs['iscolor'])?$attrs['iscolor']:0}}">
                {{--<p>&nbsp;&nbsp;--}}
                    {{--原颜色：@if(isset($attrs['iscolor'])&&$attrs['iscolor']&&isset($attrs['color'])&&$attrs['color'])<a href="javascript:;" style="padding:0 15px;border:2px solid #333;font-size:12px;background:{{$attrs['color']}};"></a>@else 默认 @endif--}}
                {{--</p>--}}
                <p style="position:relative;top:-6px;">&nbsp;&nbsp;
                    选颜色：<input type="color" title="点击选择颜色" value="{{(isset($attrs['color'])&&$attrs['color'])?$attrs['color']:'#000000'}}" onchange="setFontColor(this.value)">
                    <input type="hidden" name="color" value="{{(isset($attrs['color'])&&$attrs['color'])?$attrs['color']:'#000000'}}">
                </p>
            </div>

            <p>字大小：<input type="text" title="可输入12~30px" name="fontsize" style="width:50px;" value="{{(isset($attrs['fontsize'])&&$attrs['fontsize'])?$attrs['fontsize']:'16'}}" onchange="setAttr()"> px</p>
            <p style="font-size:12px;">注：未填选的代表默认</p>
        </div>
    </div>
    <div class="menu"><a href="javascript:;" title="点击切换" onclick="getMenu(3);">内容：</a>
        <div class="menutab" id="menu3" style="display:{{(isset($menutab)&&$menutab==3)?'block':'none'}};">
            <label><input type="radio" class="radio" name="iscon1" value="1" {{(!isset($cons['iscon'])||(isset($cons['iscon'])&&$cons['iscon']==1))?'checked':''}} onclick="getCon(1)"> 文字 </label>
            <label><input type="radio" class="radio" name="iscon2" value="2" {{(isset($cons['iscon'])&&$cons['iscon']==2)?'checked':''}} onclick="getCon(2)"> 图片 </label>
            <input type="hidden" name="iscon" value="{{(isset($cons['iscon'])&&$cons['iscon']==2)?$cons['iscon']:1}}">

            <span id="istext"><br>
                <textarea name="text" id="text" onchange="setText()">{{(isset($cons['iscon'])&&$cons['iscon']==1&&$cons['text'])?$cons['text']:'文字待输入...'}}</textarea>
            </span>

            <form action="{{DOMAIN}}admin/t/{{$product['id']}}/layer/toimg/{{$layers['id']}}" method="POST" data-am-validator enctype="multipart/form-data" id="isimg" style="display:none;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="pro_id" value="{{$product['id']}}">
                <input type="hidden" name="layerid" value="{{$layers['id']}}">
                <script src="{{PUB}}assets/js/local_pre.js"></script>
                <input type="button" class="submit" value="[ 找图 ]" onclick="path.click()" style="width:90px;cursor:pointer;"><br>
                <input type="file" id="path" style="display:none" onchange="url_file.value=this.value;loadImageFile();" name="url_ori">
                <input type="text" placeholder="本地图片地址" name="url_file" readonly style="width:150px;">
                <div id="preview" style="width:100px;height:100px;border:1px dotted grey;
    filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale);"></div>
                <input type="submit" title="点击确定更新" value="确定上传">
            </form>
        </div>
    </div>
    <div style="height:50px;"></div>
    {{--@if($hasRedis)--}}
    {{--<div id="submit">--}}
        {{--<a href="javascript:;" title="点击取消修改" class="submit" onclick="cancel()">取消修改</a><a href="javascript:;" title="点击确定修改" class="submit" onclick="save()">确定修改</a>--}}
    {{--</div>--}}
    {{--@endif--}}
@else
    <div class="menu"><a href="javascript:;">待添加...</a></div>
@endif
</div>


<script src="{{PUB}}assets/js/jquery-1.10.2.min.js"></script>
<script>
    function getMenu(tab){
        var t = 100;
        $(".menutab").hide(t);
        if (tab==1) {
            $("#menu1").show(t);
        } else if (tab==2) {
            $("#menu2").show(t);
        } else if (tab==3) {
            $("#menu3").show(t);
//            setText();
        } else if (tab==4) {
            $("#menu4").show(t);
        }
    }
    function isborder(tab){
        $("input[name='isborder']")[0].value = tab;
        if (tab==0) {
            $("#isborder").hide();
        } else {
            $("#isborder").show();
        }
    }
    function setBoderColor(val){    //设置边框颜色
        $("input[name='border3']")[0].value = val; setAttr();
    }
    function isbg(tab){
        $("input[name='isbg']")[0].value = tab;
        if (tab==0) {
            $("#isbg").hide();
        } else {
            $("#isbg").show();
        }
    }
    function isbigbg(tab){
        $("input[name='isbigbg']")[0].value = tab;
        if (tab==0) {
            $("#isbigbg").hide();
        } else {
            $("#isbigbg").show();
        }
    }
    function setBgColor(val){       //设置背景颜色
        $("input[name='bg']")[0].value = val; setAttr();
    }
    function setBigBgColor(val){       //设置大背景颜色
        $("input[name='bigbg']")[0].value = val; setAttr();
    }
    function iscolor(tab){
        $("input[name='iscolor']")[0].value = tab;
        if (tab==0) {
            $("#iscolor").hide();
        } else {
            $("#iscolor").show();
        }
    }
    function setFontColor(val){       //设置文字颜色
        $("input[name='color']")[0].value = val; setAttr();
    }
    function getCon(tab){
        $("input[name='iscon']")[0].value = tab;
        if (tab==1) {
            $("#istext").show();
            $("#isimg").hide();
//            setText();
        } else {
            $("#istext").hide();
            $("#isimg").show();
        }
    }

    $.ajaxSetup({headers : {'X-CSRF-TOKEN':$('input[name="_token"]').val()}});
    var layerid = $("input[name='layerid']").val();
    var pro_id = $("input[name='pro_id']").val();
    //ajax更新动画设置数据
    function setLayer(){
        var name = $("input[name='name']").val();
        var delay = $("input[name='delay']").val();
        var timelong = $("input[name='timelong']").val();
        var data = {
            'pro_id':pro_id,
            'layerid':layerid,
            'name':name,
            'delay':delay,
            'timelong':timelong
        };
        $.ajax({
            type: 'POST',
            url: '/admin/pro/'+pro_id+'/layer/tolayer',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.code!=0) {
                    alert(data.msg);return;
                }
                window.location.href = '';
            }
        });
    }
    //ajax更新属性数据
    function setAttr(){
        var width = $("input[name='width']").val();
        var height = $("input[name='height']").val();
        var isborder = $("input[name='isborder']").val();
        var border1 = $("input[name='border1']").val();
        var border2 = $("select[name='border2']").val();
        var border3 = $("input[name='border3']").val();
        var isbg = $("input[name='isbg']").val();
        var bg = $("input[name='bg']").val();
        var iscolor = $("input[name='iscolor']").val();
        var color = $("input[name='color']").val();
        var fontsize = $("input[name='fontsize']").val();
        var isbigbg = $("input[name='isbigbg']").val();
        var bigbg = $("input[name='bigbg']").val();
        var data = {
            'pro_id':pro_id,
            'layerid':layerid,
            'width':width,
            'height':height,
            'isborder':isborder,
            'border1':border1,
            'border2':border2,
            'border3':border3,
            'isbg':isbg,
            'bg':bg,
            'iscolor':iscolor,
            'color':color,
            'fontsize':fontsize,
            'isbigbg':isbigbg,
            'bigbg':bigbg
        };
        $.ajax({
            type: 'POST',
            url: '/admin/pro/'+pro_id+'/layer/toattr',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.code!=0) {
                    alert(data.msg);return;
                }
                window.location.href = '';
            }
        });
    }
    //ajax更新文字内容数据
    function setText(){
        var iscon = $("input[name='iscon']").val();
        var text = $("textarea[name='text']").val();
        var data = {
            'pro_id':pro_id,
            'layerid':layerid,
            'iscon':iscon,
            'text':text
        };
        $.ajax({
            type: 'POST',
            url: '/admin/pro/'+pro_id+'/layer/totext',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.code!=0) {
                    alert(data.msg);return;
                }
                window.location.href = '';
            }
        });
    }
    //取消、删除几个缓存
    function cancel(){
        window.location.href = '{{DOMAIN}}admin/pro/'+pro_id+'/layer/cancel/'+layerid;
    }
    //缓存入库
    function save(){
        window.location.href = '{{DOMAIN}}admin/pro/'+pro_id+'/layer/save/'+layerid;
    }
</script>