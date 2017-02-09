@extends('layout.main')
@section('content')
    <style>
        #preview { margin:10px 100px;width:800px;height:450px;border:5px solid #333;box-shadow:0 0 20px black; }
    </style>

    <div class="online_list">
        @include('member.common.menu')
        <table class="list">
            <tr>
                <td colspan="5"><a href="javascript:;" title="点击返回上一页"
                                   onclick="window.location.href='{{DOMAIN}}u/pro/{{$product['id']}}/layer'">返回上一页</a>
                </td>
            </tr>
            <tr>
                <td>产品名称：{{$product['name']}}</td>
                <td>动画层：{{$layer['name']}}</td>
                <td><a href="" title="点此刷新重放，或按F5">重放</a></td>
            </tr>
            <tr><td colspan="5" style="border:0;">
                    <iframe id="preview" frameborder=0 scrolling=no src="{{DOMAIN}}u/pro/{{$product['id']}}/{{$layer['id']}}/keyvals"></iframe>
                </td></tr>
        </table>
    </div>
@stop