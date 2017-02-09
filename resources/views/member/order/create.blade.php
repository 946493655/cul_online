@extends('layout.main')
@section('content')
    <style>
        table { font-size:20px; }
        table td { line-height:50px; }
        select { padding:2px 5px;border:1px solid lightgrey; }
        .submit { padding:5px 20px;color:#e5e5e5;font-family:'微软雅黑';border:0;font-size:20px;background:orangered;cursor:pointer; }
        input:hover.submit { color:white; }
    </style>

    <div class="online_list">
        @include('member.common.menu')

        <form action="{{DOMAIN}}u/render/{{$product['id']}}" method="POST" data-am-validator enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <table class="list">
                <tr><td colspan="5" style="color:grey;text-align:center;">渲染设置面板</td></tr>
                <tr>
                    <td>产品：{{$product['name']}}</td>
                    <td>用户：{{Session::get('user.username')}}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>输出宽高比：
                        <select name="format" onchange="getFM(this.value)">
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
                    <td>优惠：</td>
                </tr>
                <tr>
                    <td colspan="5">输出格式：MP4</td>
                </tr>
                <tr>
                    <td colspan="5">更新的记录(每个5元)：</td>
                </tr>
                <tr>
                    <td>总计费用：??</td>
                    <td>可用福利：??元 <a href="">去兑换</a> (优先支付)</td>
                    <td>实际应支付：？？</td>
                </tr>
                <tr>
                    <td colspan="5"><span style="font-size:14px;">注意：提交后，再扫码支付</span></td>
                </tr>
                <tr>
                    <td colspan="5" style="border:0;text-align:center;">
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
        }
    </script>
@stop