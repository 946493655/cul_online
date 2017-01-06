{{--图片上传模板--}}


<style>
    #file { width:350px; }
    #btn { width:100px;border:0;color:white;background:grey;cursor:pointer; }
    input:hover#btn { color:orangered; }
    #preview { margin-bottom:10px;width:160px;height:120px;border:1px dotted #5bc0de;
        filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale); }
</style>
<script src="{{PUB}}assets/js/local_pre.js"></script>
<input type="text" id="file" placeholder="本地图片地址" name="url_file" readonly>
<input type="button" id="btn" value="[找图]" onclick="path.click()">
<input type="file" id="path" style="display:none" onchange="url_file.value=this.value;loadImageFile();" name="url_ori">
<div id="preview"></div>
<br>