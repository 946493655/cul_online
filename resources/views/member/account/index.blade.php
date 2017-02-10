@extends('layout.main')
@section('content')
    <style>
        .convert a { color:#FFFFFF; }
        .convert input { padding:3px;width:50px; }
    </style>
    <div class="online_list">
        @include('member.common.menu')
        <table class="list" style="padding-left:20px;font-size:20px;line-height:50px;">
            <tr><td colspan="5" style="text-align:center;"><h3>您的账户中心</h3></td></tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>用户名：{{$userInfo['username']}}</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>福利数：{{$wallet['weal']}}元
                    <span style="font-size:14px;">(下面签到、金币、红包可以兑换福利)</span>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>签到数：{{$wallet['sign']}}</td>
                <td>&nbsp;</td>
                <td class="convert">
                    <input type="text" name="sign" placeholder="福利数"> x {{$wealBySign}}
                    <a href="javascript:;" title="点击兑换" onclick="getWealBySign()">去兑换</a>
                    <span style="font-size:14px;">({{$wealBySign}}签到兑换1福利)</span>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>金币数：{{$wallet['gold']}}</td>
                <td>&nbsp;</td>
                <td class="convert">
                    <input type="text" name="gold" placeholder="福利数"> x {{$wealByGold}}
                    <a href="javascript:;" title="点击兑换" onclick="getWealByGold()">去兑换</a>
                    <span style="font-size:14px;">({{$wealByGold}}金币兑换1福利)</span>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>红包额度：{{$wallet['tip']}}</td>
                <td>&nbsp;</td>
                <td class="convert">
                    <input type="text" name="tip" placeholder="福利数"> x {{$wealByTip}}
                    <a href="javascript:;" title="点击兑换" onclick="getWealByTip()">去兑换</a>
                    <span style="font-size:14px;">({{$wealByTip}}额度兑换1福利)</span>
                </td>
            </tr>
            <tr><td colspan="5" style="text-align:center;border:0;">
                    <a href="javascript:;" onclick="history.go(-1);" title="点击返回上一页"><h3>返 回</h3></a>
                </td></tr>
        </table>
    </div>

    <script>
        function getWealBySign(){
            var sign = $("input[name='sign']").val();
            if (sign==0 || sign=='') { alert('务必填写数量！');return; }
            window.location.href = '{{DOMAIN}}myinfo/wealbysign/'+sign;
        }
        function getWealByGold(){
            var gold = $("input[name='gold']").val();
            if (gold==0 || gold=='') { alert('务必填写数量！');return; }
            window.location.href = '{{DOMAIN}}myinfo/wealbygold/'+gold;
        }
        function getWealByTip(){
            var tip = $("input[name='tip']").val();
            if (tip==0 || tip=='') { alert('务必填写数量！');return; }
            window.location.href = '{{DOMAIN}}myinfo/wealbytip/'+tip;
        }
    </script>
@stop