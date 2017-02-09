@extends('layout.main')
@section('content')
    <div class="online_list">
        <div class="condition">
            @include('home.common.menu')
            <div class="attr" style="height:{{count($model['cates'])>14?40:20}}px;">
                <div class="cate_s">类型：</div>
                <div class="{{$cate==0?'cate_curr':'cate_s'}}" onclick="jump(0)">所有</div>
                @foreach($model['cates'] as $kcate=>$vcate)
                <div class="{{$cate==$kcate?'cate_curr':'cate_s'}}" onclick="jump({{$kcate}})">{{ $vcate }}</div>
                @endforeach
            </div>
            {{--支付二维码--}}
            <div class="attr" style="height:20px">
                <span style="color:orangered;float:right;">支付二维码
                    <a href="javascript:;" onclick="$('#qrcode').show()"><b>点击获取</b></a>
                </span>
            </div>
        </div>

        <table class="list">
            <tr style="background:#333;">
                <td>用户</td>
                <td>作品名</td>
                <td>进度</td>
                <td>预览图</td>
                <td>申请时间</td>
            </tr>
            @if(count($datas))
                @foreach($datas as $data)
            <tr>
                <td>{{$data['uame']}}</td>
                <td>{{$data['pname']}}</td>
                <td>{{$data['statusName']}}</td>
                <td>@if($data['status']>3 && $data['thumb'])
                        <img src="{{$data['thumb']}}" width="20">
                    @else /
                    @endif</td>
                <td>{{$data['createTime']}}</td>
            </tr>
                @endforeach
            @else
                <tr><td colspan="10">没有记录</td></tr>
            @endif
        </table>
        <div style="margin-top:20px;clear:both;">@include('layout.page')</div>
    </div>

    <div class="editproduct" id="qrcode">
        <div class="mask"></div>
        <div class="qrcode">
            <img src="/assets/images/QRcode.png">
            <p>支付宝二维码(第二天更新状态)</p>
            <p>昵称：<b>斯塔克</b>
                <a href="http://wpa.qq.com/msgrd?v=3&uin=2857156840&site=qq&menu=yes" style="color:orangered;">QQ验证</a>
            </p>
            <a href="javascript:;" class="close" title="点击关闭二维码" onclick="$('.editproduct').hide()">关闭</a>
        </div>
    </div>
@stop