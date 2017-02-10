@extends('layout.main')
@section('content')
    <div class="online_list">
        @include('member.common.menu')

        <table class="list" style="text-align:center;">
            <tr style="background:#333;">
                <td>用户</td>
                <td>作品名</td>
                <td>需支付</td>
                <td>福利支付</td>
                <td>实际应支付</td>
                <td>进度</td>
                <td>预览</td>
                <td>申请时间</td>
                <td>操作</td>
            </tr>
            @if(count($datas))
                @foreach($datas as $data)
                    <tr>
                        <td>{{$data['uname']}}</td>
                        <td>{{$data['pname']}}</td>
                        <td>{{$data['money']}}元</td>
                        <td>{{$data['weal']}}元</td>
                        <td><span style="color:orangered;">{{$data['money1']}}</span>元</td>
                        <td>
                            @if($data['status']<3) <span style="color:orangered;">{{$data['statusName']}}</span>
                            @else {{$data['statusName']}}
                            @endif
                        </td>
                        <td>@if($data['status']>3 && $data['thumb'])
                                <img src="{{$data['thumb']}}" width="20">
                            @else /
                            @endif</td>
                        <td>{{$data['createTime']}}</td>
                        <td>/</td>
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