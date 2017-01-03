{{-- 前台页面脚部模板 --}}

<!-- footer网站脚部 -->
<div class="footer">
    <div style="height:0px;display:@if((isset($footSwitch)&&!$footSwitch)||!isset($footSwitch)){{'block'}}@else{{'none'}}@endif;;"></div>
    <div class="footer_center">
        <p class="footer_text">
                <a href="" title="">XXX</a>
        </p>
        <p class="footer_beizhu">{{--Copyright © 2016-2020--}} zuoshiping.com {{--All Rights Reserved 版权所有 微文化--}}</p>
    </div>
</div>
<!-- footer网站脚部 -->