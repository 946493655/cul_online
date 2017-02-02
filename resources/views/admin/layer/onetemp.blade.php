@extends('admin.main')
@section('content')
    <style>
        #preview { margin:10px 100px;width:800px;height:450px;border:5px solid #333; }
    </style>

    <div class="online_list">
        @include('admin.common.menu')
        <table class="list">
            <tr><td colspan="5">
                    <a href="javascript:;" style="color:lightgrey;"
                       onclick="window.location.href='{{DOMAIN}}admin/t/{{$tempid}}/layer';">返回上一页</a>
                </td></tr>
            <tr><td>模板名称：</td></tr>
            <tr><td colspan="5" style="border:0;">
                    <iframe id="preview" frameborder=0 scrolling=no src="{{DOMAIN}}admin/temp/preview/layers/{{$tempid}}"></iframe>
                </td>
            </tr>
        </table>
    </div>
@stop