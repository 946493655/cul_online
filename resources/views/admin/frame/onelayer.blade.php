@extends('admin.main')
@section('content')
    <style>
        #preview { margin:10px auto;width:800px;height:450px;border:5px solid #333; }
    </style>

    <div style="height:100px;"></div>
    <iframe id="preview" frameborder=0 scrolling=no src="{{DOMAIN}}admin/t/{{$temp['id']}}/{{count($datas)?$datas[0]['id']:0}}/keyvals"></iframe>
@stop