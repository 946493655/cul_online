{{--单个动画层的key-value框架--}}

<style>
    body { margin:0;padding:0;font-family:'微软雅黑'; }
    #preframe {
        width:800px; height:450px;
        @if(isset($attrs['isbigbg'])&&$attrs['isbigbg']) background:{{$attrs['bigbg']}};@endif
    }

    /*以下样式动态获取*/
    #attr {
        padding:5px;
        overflow:hidden;
        position:absolute;
        @if($attrs)
            width:{{$attrs['width']?$attrs['width']:'300'}}px;
            height:{{$attrs['height']?$attrs['height']:'100'}}px;
            @if($attrs['isborder'])
                border:{{$attrs['border1'].'px '.$layerModel['border2s'][$attrs['border2']].' '.$attrs['border3']}};
            @endif
            @if($attrs['isbg']) background:{{$attrs['bg']}}; @endif
            @if($attrs['iscolor']) color:{{$attrs['color']}}; @endif
            @if($attrs['fontsize']) font-size:{{$attrs['fontsize'].'px'}}; @endif
        @else
            width:300px;
            height:100px;
            color:grey;
            background:white;
        @endif
        /*animation: name duration timing-function delay iteration-count direction;*/
        /*animation-name*/
        /*animation-duration*/
        /*animation-timing-function*/
        /*animation-delay*/
        /*animation-iteration-count*/
        /*animation-direction*/
        @if($layer)
            animation-name:{{$layer['a_name']}};
            animation-duration:{{$layer['timelong']}}s;
            {{--animation-delay:{{$layer['delay']}}s;--}}
            {{--animation-timing-function:ease;--}}
            {{--animation-direction:normal;--}}
        @endif
    }

    @if($layer)
        {{'@keyframes '.$layer['a_name'].' {'}}
            @if($frameLeft)
            @foreach($frameLeft as $left)
                {{$left['per'].'% { left:'.$left['val'].'px; }'}}
            @endforeach
            @endif
            @if($frameTop)
            @foreach($frameTop as $top)
                {{$top['per'].'% { top:'.$top['val'].'px; }'}}
            @endforeach
            @endif
            @if($frameOpacity)
            @foreach($frameOpacity as $opacity)
                {{$opacity['per'].'% { opacity:'.$opacity['val']/100 .'; }'}}
            @endforeach
            @endif
        {{'}'}}
        /*Safari and Chrome*/
        {{'@-webkit-keyframes '.$layer['a_name'].' {'}}
            @if($frameLeft)
            @foreach($frameLeft as $left)
                {{$left['per'].'% { left:'.$left['val'].'px; }'}}
            @endforeach
            @endif
            @if($frameTop)
            @foreach($frameTop as $top)
                {{$top['per'].'% { top:'.$top['val'].'px; }'}}
            @endforeach
            @endif
            @if($frameOpacity)
            @foreach($frameOpacity as $opacity)
                {{$opacity['per'].'% { opacity:'.$opacity['val']/100 .'; }'}}
            @endforeach
            @endif
        {{'}'}}
    @endif
</style>

<div id="preframe">
    <div id="attr">
        @if($cons && $cons['iscon']==1) {{$cons['text']}}
        @elseif($cons && $cons['iscon']==2) <img src="{{$cons['img']}}">
        @else 文字待输入...
        @endif
    </div>
</div>