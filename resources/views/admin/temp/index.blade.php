@extends('admin.main')
@section('content')
    <div class="online_list">
        @include('admin.common.menu')

        <div class="searchlist">
            @if(env('APP_ENV')=='local')
                <a href="javascript:void(0);" onclick="getEditPro2()">清空表</a>
            @endif
            <a href="javascript:void(0);" onclick="getEditPro1()" title="新产品增加">+ 添加产品</a>
        </div>
        <div class="list">
            @if(count($datas)>1)
                @foreach($datas as $kdata=>$data)
                    @if(is_numeric($kdata))
            <a href="{{DOMAIN}}admin/temp/{{$data['id']}}" title="点击进入调整 {{$data['name']}}">
                <div class="prolist">
                    <div class="pro_one">
                        @if($data['thumb'])
                        <img src="{{$data['thumb']}}" width="210">
                        @else
                        缩略图待添加
                        @endif
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
        <form action="{{DOMAIN}}admin/temp" method="POST" data-am-validator>
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
    {{--弹出框：添加产品--}}
    <div class="editproduct" id="editproduct2">
        <div class="mask"></div>
        <div class="msg">
            <p style="text-align:center;">
                确定要删除 模板列表 么？ <br><br>
                <a href="{{DOMAIN}}admin/temp/clear">确定清空</a>
                <a href="javascript:void(0);" onclick="closeEditPro2()">取消</a>
            </p>
        </div>
    </div>

    <script>
        function getEditPro1(){ $("#editproduct1").show(); }
        function closeEditPro1(){ $("#editproduct1").hide(); }
        function getEditPro2(){ $("#editproduct2").show(); }
        function closeEditPro2(){ $("#editproduct2").hide(); }
    </script>
@stop