@extends('layout.main')
@section('content')
    <style>
        table { font-size:20px; }
        table td { line-height:50px; }
        select { padding:3px 5px;border:1px solid lightgrey; }
        .submit { padding:5px 30px;color:#e5e5e5;font-family:'微软雅黑';border:0;font-size:20px;background:orangered;cursor:pointer; }
        input:hover.submit { color:white; }
        span.gengxin { color:orangered; }
    </style>

    <div class="online_list">
        @include('member.common.menu')

        <form action="{{DOMAIN}}o" method="POST" data-am-validator enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="pro_id" value="{{$product['id']}}">
            <table class="list">
                <tr><td colspan="3" style="color:grey;text-align:center;">渲染设置面板</td></tr>
                <tr>
                    <td>产品：{{$product['name']}}</td>
                    <td>动画层数：{{$product['layerCount']}}</td>
                    <td>用户：{{Session::get('user.username')}}</td>
                </tr>
                <tr>
                    <td>输出宽高比：
                        <select name="kformat" onchange="getFM(this.value)">
                            @foreach($model['formats'] as $kformat=>$format)
                                <option value="{{$kformat}}" {{$kformat==3 ? 'selected' : ''}}>
                                    {{$format}} {{$model['formatNames'][$kformat]}}</option>
                            @endforeach
                        </select>
                            <span style="display:none;">
                                @foreach($model['formatMoneys'] as $kfm=>$formatMoney)
                                    <input type="hidden" name="fm{{$kfm}}" value="{{$formatMoney}}">
                                @endforeach
                            </span>
                    </td>
                    <td>费用：
                        <span id="fm">{{$model['formatMoneys'][3]}}元</span>
                        <input type="hidden" name="formatMoney" value="{{$model['formatMoneys'][3]}}">
                    </td>
                    <td>{{--优惠：--}}</td>
                </tr>
                <tr>
                    <td colspan="2">记录更新：
                        动画设置(<span class="gengxin">{{$userModifyArr['layerNum']}}</span>个) &nbsp;
                        动画内容(<span class="gengxin">{{$userModifyArr['conNum']}}</span>个) &nbsp;
                        动画属性(<span class="gengxin">{{$userModifyArr['attrNum']}}</span>个) &nbsp;
                        @if($userModifyArr['frameNum'])
                        关键帧(<span class="gengxin">{{$userModifyArr['frameNum']}}</span>个) &nbsp;
                        @endif
                    </td>
                    <td>共：
                        <span class="gengxin">{{$userModifyArr['countNum']*$unitPrice}}</span>元
                        ({{$unitPrice}}元/个)
                        <input type="hidden" name="countPrice" value="{{$userModifyArr['countNum']*$unitPrice}}">
                    </td>
                </tr>
                <tr>
                    <td>总计费用：
                        <span class="gengxin" id="money">{{$userModifyArr['countNum']*$unitPrice+70}}</span>元
                        <input type="hidden" name="money" value="{{$userModifyArr['countNum']*$unitPrice+70}}">
                    </td>
                    <td>可用福利：<span class="gengxin">{{$wallet['weal']}}</span>元
                        <a href="{{DOMAIN}}myinfo" title="点击兑换">去兑换</a> (优先支付)
                        <input type="hidden" name="weal" value="{{$wallet['weal']}}">
                    </td>
                    <td>实际应支付：
                        <span class="gengxin" id="money1">
                            {{$userModifyArr['countNum']*$unitPrice+70-$wallet['weal']}}</span>元
                        <input type="hidden" name="money1"
                               value="{{$userModifyArr['countNum']*$unitPrice+70-$wallet['weal']}}">
                    </td>
                </tr>
                {{--<tr>--}}
                    {{--<td colspan="3">优惠情况：</td>--}}
                {{--</tr>--}}
                <tr>
                    <td colspan="3">输出格式选择：标配(mov、wmv、avi、mp4)，需要其他格式请联系
                        <a href="http://wpa.qq.com/msgrd?v=3&uin=2857156840&site=qq&menu=yes" title="点击QQ通话">斯塔克</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><span style="font-size:14px;">注意：提交后，再扫码支付</span></td>
                </tr>
                <tr>
                    <td colspan="3" style="border:0;text-align:center;">
                        <input type="submit" class="submit" value="确 定" title="确定设置，提交渲染申请">
                        <input type="button" class="submit" value="取 消" onclick="$('.popup').hide(100);" title="取消设置">
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <script>
        //渲染输出设置
        function getFM(key){
            var fm = $("input[name='fm"+key+"']").val();
            $("#fm").html(fm + '元');
            $("input[name='formatMoney']")[0].value = fm;
            $("#money").html(fm);
            $("input[name='money']")[0].value = fm;
            //算出总价
            var countPrice = $("input[name='countPrice']").val();
            var weal = $("input[name='weal']").val();
            var totalPrice = countPrice*1 + fm*1 -weal*1;
//            alert(totalPrice);return;
            $("#money1").html(totalPrice);
            $("input[name='money1']")[0].value = totalPrice;
        }
    </script>
@stop