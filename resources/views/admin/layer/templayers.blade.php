{{--单个模板的动画layers框架--}}

<style>
    body { margin:0;padding:0;font-family:'微软雅黑'; }
    #preframe {
        width:800px; height:450px;
        background:#000000;
    }
    .layer { width:800px; height:450px; }
    .attr { padding:5px; overflow:hidden; position:absolute; }

    /*以下样式动态获取*/
    @if($layers)
    @foreach($layers as $layer)
        {{'.l_'.$layer['id'].' {'}}
            @if(isset($layer['attr']['isbigbg'])&&$layer['attr']['isbigbg'])
                {{'background:'.$layer['attr']['bigbg'].';'}}
            @endif
        {{'}'}}
        {{--样式属性--}}
        {{'#'.$layer['a_name'].' {'}}
        @if($attrs=$layer['attr'])
            @if($attrs['width']){{'width:'.$attrs['width'].'px;'}}@else{{'width:300px;'}}@endif
            @if($attrs['height']){{'height:'.$attrs['height'].'px;'}}@else{{'height:100px;'}}@endif
            @if($attrs['isborder'])
                {{'border:'.$attrs['border1'].'px '.$layerModel['border2s'][$attrs['border2']].' '.$attrs['border3'].';'}}
            @endif
            @if($attrs['isbg']) {{'background:'.$attrs['bg'].';'}} @endif
            @if($attrs['iscolor']) {{'color:'.$attrs['color'].';'}} @endif
            @if($attrs['fontsize']) {{'font-size:'.$attrs['fontsize'].'px;'}} @endif
        @else
            {{'width:300px;height:100px;color:grey;background:white;'}}
        @endif
        @if(!$layer['leftArr']){{'left:0;'}}@endif
        @if(!$layer['topArr']){{'top:0;'}}@endif
        {{--@if(!$layer['opacityArr']){{'opacity:1;'}}@endif--}}
        /*animation-name*/
        /*animation-duration*/
        /*animation-timing-function*/
        /*animation-delay*/
        /*animation-iteration-count*/
        /*animation-direction*/
        {{'animation-name:'.$layer['a_name'].';'}}
        {{'animation-duration:'.$layer['timelong'].'s;'}}
        {{'animation-delay:'.$layer['delay'].'s;'}}
        {{--{{'animation-timing-function:ease;'}}--}}
        {{--{{'animation-direction:normal;'}}--}}
        {{'}'}}

        {{--关键帧--}}
        {{'@keyframes '.$layer['a_name'].' {'}}
            @if($frameLeft=$layer['leftArr'])
            @foreach($frameLeft as $left)
                {{$left['per'].'% { left:'.$left['val'].'px; }'}}
            @endforeach
            @endif
            @if($frameTop=$layer['topArr'])
            @foreach($frameTop as $top)
                {{$top['per'].'% { top:'.$top['val'].'px; }'}}
            @endforeach
            @endif
            @if($frameOpacity=$layer['opacityArr'])
            @foreach($frameOpacity as $opacity)
                {{$opacity['per'].'% { opacity:'.$opacity['val']/100 .'; }'}}
            @endforeach
            @endif
        {{'}'}}
        /*Safari and Chrome*/
        {{'@-webkit-keyframes '.$layer['a_name'].' {'}}
        @if($frameLeft=$layer['leftArr'])
        @foreach($frameLeft as $left)
            {{$left['per'].'% { left:'.$left['val'].'px; }'}}
        @endforeach
        @endif
        @if($frameTop=$layer['topArr'])
        @foreach($frameTop as $top)
            {{$top['per'].'% { top:'.$top['val'].'px; }'}}
        @endforeach
        @endif
        @if($frameOpacity=$layer['opacityArr'])
        @foreach($frameOpacity as $opacity)
            {{$opacity['per'].'% { opacity:'.$opacity['val']/100 .'; }'}}
        @endforeach
        @endif
    {{'}'}}
    @endforeach
    @endif
</style>

<div id="preframe">
    @if($layers)
    @foreach($layers as $layer)
    <div class="layer l_{{$layer['id']}}">
        <div class="attr" id="{{$layer['a_name']}}">
            @if($layer['con'] && $layer['con']['iscon']==1) {{$layer['con']['text']}}
            @elseif($layer['con'] && $layer['con']['iscon']==2) <img src="{{$layer['con']['img']}}">
            @else 内容待输入...
            @endif
        </div>
    </div>
    @endforeach
    @endif
</div>