<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiTempFrame;
use App\Api\ApiOnline\ApiTempLayer;
use App\Api\ApiOnline\ApiTempPro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as AjaxRequest;
use Illuminate\Support\Facades\Input;
use Redis;

class LayerController extends BaseController
{
    /**
     * 动画修改
     */

    protected $rediskey = 'online_admin_layer_';

    public function index($tempid)
    {
        $rstTemp = ApiTempPro::show($tempid);
        if ($rstTemp['code']!=0) {
            echo "<script>alert('".$rstTemp['msg']."');history.go(-1);</script>";exit;
        }
        $rst = ApiTempLayer::index($tempid);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'temp' => $rstTemp['data'],
            'datas' => $rst['data'],
        ];
        return view('admin.layer.index',$result);
    }

    public function store($tempid)
    {
        if (!\Session::has('admin')) {
            echo "<script>alert('没有登录！');window.location.href='/admin/login';</script>";exit;
        }
        if ($tempid!=Input::get('tempid')) {
            echo "<script>alert('参数错误！');history.go(-1);</script>";exit;
        }
        $data = [
            'name'  =>  Input::get('name'),
            'tempid'    =>  Input::get('tempid'),
            'delay'     =>  Input::get('delay'),
            'timelong'  =>  Input::get('timelong'),
        ];
        $rst = ApiTempLayer::add($data);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['data']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/t/'.$tempid.'/layer');
    }

    /**
     * 根据 id 获取动画模板
     */
    public function show($tempid,$id)
    {
        $rstTemp = ApiTempPro::show($tempid);
        if ($rstTemp['code']!=0) {
            echo "<script>alert('".$rstTemp['msg']."');history.go(-1);</script>";exit;
        }
        //动画属性：先读缓存，再读数据表记录
        $rstLayer = ApiTempLayer::show($id);
        if ($rstLayer['code']!=0) {
            echo "<script>alert('".$rstLayer['msg']."');history.go(-1);</script>";exit;
        }
        if ($rstRedis=Redis::get($this->rediskey.$id)) {
            $layerArr = unserialize($rstRedis);
            $layers = isset($layerArr['layer'])?$layerArr['layer']:[];
            $attrs = isset($layerArr['attr'])?$layerArr['attr']:[];
            $cons = isset($layerArr['con'])?$layerArr['con']:[];
            $menutab = isset($layerArr['menu']['menutab'])?$layerArr['menu']['menutab']:1;
        }
        //动画、属性、内容，没有的话去接口获取
        $apilayer = $rstLayer['data'];
        if (!isset($layers) || (isset($layers)&&!$layers)) {
            $layers = [
                'id'  =>  $apilayer['id'],
                'name'  =>  $apilayer['name'],
                'delay' =>  $apilayer['delay'],
                'timelong'  =>  $apilayer['timelong'],
            ];
        }
        if ((!isset($attrs) || (isset($attrs)&&!$attrs)) && $apilayer['attr']) {
            $attrs = $apilayer['attr'] ? unserialize($apilayer['attr']) : [];
        }
        if ((!isset($cons) || (isset($cons)&&!$cons)) && $apilayer['con']) {
            $conArr = $apilayer['con'] ? unserialize($apilayer['con']) : [];
            $cons = [
                'iscon' =>  $conArr['iscon'],
                'text'  =>  $conArr['text'],
                'img'   =>  $conArr['img'],
            ];
        }
//        dd($layers,$attrs,$cons);
        $result = [
            'temp' => $rstTemp['data'],
            'model' => $this->getModel(),
            'layers' => isset($layers) ? $layers : [],
            'attrs' => isset($attrs) ? $attrs : [],
            'cons' => isset($cons) ? $cons : [],
            'menutab' => isset($menutab) ? $menutab : 1,
        ];
        return view('admin.layer.iframe',$result);
    }


    /**
     * ajax更新动画设置数据
     */
    public function setLayer()
    {
        if (AjaxRequest::ajax()) {
            if (!Input::get('name') || !Input::get('timelong')) {
                echo json_encode(array('code'=>-1, 'msg'=>'数据不对！'));exit;
            }
            $rediskey = $this->rediskey.Input::get('layerid');
            if ($rstRedis=Redis::get($rediskey)) {
                $data = unserialize($rstRedis);
            }
            $data['layer'] = [
                'id'  =>  Input::get('layerid'),
                'name'  =>  Input::get('name'),
                'delay'     =>  Input::get('delay'),
                'timelong'  =>  Input::get('timelong'),
            ];
            $data['menu']['menutab'] = 1;
            Redis::setex($rediskey,$this->redisTime,serialize($data));
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-2, 'msg'=>'参数错误！'));exit;
    }

    /**
     * ajax更新属性数据
     */
    public function setAttr()
    {
        if (AjaxRequest::ajax()) {
//            dd(Input::all());
            //验证宽高
            if (Input::get('width')!='' && floor(Input::get('width'))!=Input::get('width')) {
                echo json_encode(array('code'=>-1, 'msg'=>'宽度只能是正整数！'));exit;
            }
            if (Input::get('height')!='' && !is_int(Input::get('height'))) {
                echo json_encode(array('code'=>-1, 'msg'=>'高度度只能是正整数！'));exit;
            }
            //验证边框
            if (Input::get('isborder') && (!Input::get('border1')||!Input::get('border2')||!Input::get('border3'))) {
                echo json_encode(array('code'=>-1, 'msg'=>'边框填选错误！'));exit;
            } elseif (Input::get('border1')>10 && floor(Input::get('border1'))!=Input::get('border1')) {
                echo json_encode(array('code'=>-1, 'msg'=>'边框厚度只能是 1-10 正整数！'));exit;
            }
            //验证文字尺寸
            if (Input::get('fontsize')!='' && (floor(Input::get('fontsize')!=Input::get('fontsize'))||Input::get('fontsize')<12||Input::get('fontsize')>30)) {
                echo json_encode(array('code'=>-1, 'msg'=>'文字大小须在 12-30 之间整数！'));exit;
            }
            $rediskey = $this->rediskey.Input::get('layerid');
            if ($rstRedis=Redis::get($rediskey)) {
                $data = unserialize($rstRedis);
            }
            $data['menu']['menutab'] = 2;
            $data['attr'] = [
                'width'     =>  Input::get('width'),
                'height'    =>  Input::get('height'),
                'isborder'  =>  Input::get('isborder'),
                'border1'   =>  Input::get('border1'),
                'border2'   =>  Input::get('border2'),
                'border3'   =>  Input::get('border3'),
                'isbg'      =>  Input::get('isbg'),
                'bg'        =>  Input::get('bg'),
                'iscolor'   =>  Input::get('iscolor'),
                'color'     =>  Input::get('color'),
                'fontsize'  =>  Input::get('fontsize'),
            ];
            Redis::setex($rediskey,$this->redisTime,serialize($data));
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-2, 'msg'=>'参数错误！'));exit;
    }

    /**
     * ajax更新文字内容数据
     */
    public function setText()
    {
        if (AjaxRequest::ajax()) {
            //验证内容是否为文字
            if (Input::get('iscon')!=1 || !Input::get('text')) {
                echo json_encode(array('code'=>-1, 'msg'=>'文字填写错误！'));exit;
            } elseif (mb_strlen(Input::get('text'))>255) {
                echo json_encode(array('code'=>-3, 'msg'=>'文字不能多于255个字符！'));exit;
            }
            $rediskey = $this->rediskey.Input::get('layerid');
            if ($rstRedis=Redis::get($rediskey)) {
                $data = unserialize($rstRedis);
            }
            $data['menu']['menutab'] = 3;
//            $data['con'] = [
//                'iscon' =>  Input::get('iscon'),
//                'text' =>  Input::get('text'),
//            ];
            $data['con']['iscon'] = Input::get('iscon');
            $data['con']['text'] = Input::get('text');
            Redis::setex($rediskey,$this->redisTime,serialize($data));
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-2, 'msg'=>'参数错误！'));exit;
    }

    /**
     * 更新图片内容数据
     */
    public function setImg($tempid,$id)
    {
        if (!array_key_exists('url_ori',Input::all())) {
            echo "<script>alert('未上传图片！');history.go(-1);</script>";exit;
        }
        //先去除缓存中、表中记录的老图片，再上传新图片
        $oldImgArr = array();
        $rediskey = $this->rediskey.Input::get('layerid');        //缓存中
        if ($rstRedis=Redis::get($rediskey)) {
            $data = unserialize($rstRedis);
            if (isset($data['con']['img']) && $data['con']['img']) {
                $imgArr = explode('/',$data['con']['img']);
                if (mb_substr($imgArr[0],0,4)=='http') {
                    unset($imgArr[0]); unset($imgArr[1]); unset($imgArr[2]);
                    $img = implode('/',$imgArr);
                } else {
                    $img = ltrim($data['con']['img'],'/');
                }
                if (file_exists($img)) { $oldImgArr[] = $img; }
            }
        }
        $rstLayer=ApiTempLayer::show($id);              //表中
        if ($rstLayer['code']==0 && $conObj=$rstLayer['data']['con']) {
            $conArr = unserialize($conObj);
            if (isset($conArr['img']) && $conArr['img']) {
                $imgArr1 = explode('/',$conArr['img']);
                if (mb_substr($imgArr1[0],0,4)=='http') {
                    unset($imgArr1[0]); unset($imgArr1[1]); unset($imgArr1[2]);
                    $img1 = implode('/',$imgArr1);
                } else {
                    $img1 = ltrim($conArr['img'],'/');
                }
                if (file_exists($img1)) { $oldImgArr[] = $img1; }
            }
        }
        dd(Input::all(),$tempid,$id);
        $imgStr = $this->uploadOnlyImg(Input::all(),'url_ori',$oldImgArr);
        if (!$imgStr) {
            echo "<script>alert('图片上传有误！');history.go(-1);</script>";exit;
        }
//        $data['con'] = [
//            'iscon' =>  Input::get('iscon'),
//            'img' =>  $imgStr,
//        ];
        $data['con']['iscon'] = Input::get('iscon');
        $data['con']['img'] = $imgStr;
        dd($data);
        Redis::setex($rediskey,$this->redisTime,serialize($data));
        return redirect(DOMAIN.'admin/t/'.$tempid.'/layer');
    }

    /**
     * 清除redis中对应动画数据
     */
    public function delRedis($tempid,$layerid)
    {
        $rediskey = $this->rediskey.$layerid;
        if ($rstRedis=Redis::get($rediskey)) {
            Redis::del($rediskey);
//            echo "<script>alert('操作成功！');window.location.href='/admin/t/".$tempid."/layer';</script>";exit;
            return redirect(DOMAIN.'admin/t/'.$tempid.'/layer/'.$layerid);
        }
        echo "<script>alert('没有修改过或者修改数据已过期！');history.go(-1);</script>";exit;
    }

    /**
     * redis中对应动画数据入库
     */
    public function saveRedisToDB($tempid,$layerid)
    {
        $rstLayer = ApiTempLayer::show($layerid);
        $rstRedis=Redis::get($this->rediskey.$layerid);
        if (!$rstRedis) {
            echo "<script>alert('没有修改过或者修改数据已过期！');history.go(-1);</script>";exit;
        }
        $layerArr = unserialize($rstRedis);
        //缓存没有动画设置数据，表中有记录
        if (!isset($layerArr['layer']) && $rstLayer['code']==0) {
            $layerArr['layer'] = [
                'name'      =>  $rstLayer['data']['name'],
                'delay'     =>  $rstLayer['data']['delay'],
                'timelong'  =>  $rstLayer['data']['timelong'],
            ];
        }
        //缓存没有属性数据，表中有属性数据
        if (!isset($layerArr['attr']) && $rstLayer['code']==0 && $rstLayer['data']['attr']) {
            $layerArr['attr'] = unserialize($rstLayer['data']['attr']);
        }
        //缓存没有内容数据，表中有内容数据
        if (!isset($layerArr['con']) && $rstLayer['code']==0 && $rstLayer['data']['con']) {
            $conArr = unserialize($rstLayer['data']['con']);
                $layerArr['con'] = [
                    'iscon' =>  $conArr['iscon'],
                    'text'  =>  $conArr['text'],
                    'img'   =>  $conArr['img'],
                ];
        }
        //将缓存数据入库
        $data = [
            'id'        =>  $layerid,
            'name'      =>  $layerArr['layer']['name'],
            'delay'     =>  $layerArr['layer']['delay'],
            'timelong'  =>  $layerArr['layer']['timelong'],
            'width'     =>  isset($layerArr['attr']['width'])?$layerArr['attr']['width']:'',
            'height'    =>  isset($layerArr['attr']['height'])?$layerArr['attr']['height']:'',
            'isborder'  =>  isset($layerArr['attr']['isborder'])?$layerArr['attr']['isborder']:'',
            'border1'   =>  isset($layerArr['attr']['border1'])?$layerArr['attr']['border1']:'',
            'border2'   =>  isset($layerArr['attr']['border2'])?$layerArr['attr']['border2']:'',
            'border3'   =>  isset($layerArr['attr']['border3'])?$layerArr['attr']['border3']:'',
            'isbg'      =>  isset($layerArr['attr']['isbg'])?$layerArr['attr']['isbg']:'',
            'bg'        =>  isset($layerArr['attr']['bg'])?$layerArr['attr']['bg']:'',
            'iscolor'   =>  isset($layerArr['attr']['iscolor'])?$layerArr['attr']['iscolor']:'',
            'color'     =>  isset($layerArr['attr']['color'])?$layerArr['attr']['color']:'',
            'fontsize'  =>  isset($layerArr['attr']['fontsize'])?$layerArr['attr']['fontsize']:'',
            'iscon'     =>  isset($layerArr['con']['iscon'])?$layerArr['con']['iscon']:'',
            'text'      =>  isset($layerArr['con']['text'])?$layerArr['con']['text']:'',
            'img'       =>  isset($layerArr['con']['img'])?$layerArr['con']['img']:'',
        ];
        $rstLayer2 = ApiTempLayer::modify($data);
        if ($rstLayer2['code']!=0) {
            echo "<script>alert('".$rstLayer2['msg']."');history.go(-1);</script>";exit;
        }
        Redis::del($this->rediskey.$layerid);
//        echo "<script>alert('操作成功！');window.location.href='/admin/t/".$tempid."/layer';</script>";exit;
        return redirect(DOMAIN.'admin/t/'.$tempid.'/layer/'.$layerid);
    }

    /**
     * 获取 model
     */
    public function getModel()
    {
        $rst = ApiTempLayer::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
    }
}