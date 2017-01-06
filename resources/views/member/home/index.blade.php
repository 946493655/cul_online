@extends('layout.main')
@section('content')
    <div class="online_list">
        @include('member.common.menu')

        <div class="searchlist">
            <a href="javascript:void(0);" onclick="getEditPro1()" title="新产品增加">+ 添加产品</a>
        </div>
        <div class="list">
            @if(count($datas)>1)
                @foreach($datas as $kdata=>$data)
                    @if(is_numeric($kdata))
            <a href="{{DOMAIN}}u/product/{{$data['id']}}" title="点击进入 {{$data['name']}}">
                <div class="prolist">
                    <div class="pro_one">
                        <img src="{{$data['thumb']}}" width="210">
                    </div>
                    <div class="pname"><b>{{ $data['name'] }}</b>
                        <div class="small">{{ $data['createTime'] }}</div>
                    </div>
                </div>
            </a>
                    @endif
                @endforeach
            @endif
            @for($i=0;$i<$datas['pagelist']['limit']+1-count($datas);++$i)
            <a href="javascript:void(0);">
                <div class="prolist">
                    <div class="pro_one">+待添加</div>
                    <div class="pname"><b>产品名称</b>
                        <div class="small">时间</div>
                    </div>
                </div>
            </a>
            @endfor

            <div style="clear:both;"></div>
            <div style="margin-top:20px;">@include('layout.page')</div>
        </div>
    </div>
    {{--弹出框：添加产品--}}
    <div class="editproduct" id="editproduct1">
        <div class="mask"></div>
        <form action="{{DOMAIN}}u/product" method="POST" data-am-validator>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <p style="text-align:center"><b>产品添加</b></p>
            <p>产品名称：
                <input type="text" placeholder="产品名称" minlength="2" maxlength="20" required name="name">
            </p>
            <p>产品类型：
                <select name="cate" required>
                    @foreach($model['cates'] as $kcate=>$vcate)
                        <option value="{{$kcate}}">{{$vcate}}</option>
                    @endforeach
                </select>
            </p>
            <p>产品介绍：
                <textarea rows="5" placeholder="说明文字" style="resize:none;" name="intro"></textarea>
            </p>
            <p style="text-align:center">
                <input type="submit" id="submit" title="点击确定更新" value="确定修改">
            </p>
            <a href="javascript:void(0);" title="关闭" class="close" onclick="closeEditPro1()"> X </a>
        </form>
    </div>

    <script>
        function getEditPro1(){ $("#editproduct1").show(); }
        function closeEditPro1(){ $("#editproduct1").hide(); }
    </script>
@stop