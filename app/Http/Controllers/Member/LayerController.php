<?php
namespace App\Http\Controllers\Member;

use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiProLayer;
use App\Api\ApiOnline\ApiTempLayer;
use Illuminate\Support\Facades\Request as AjaxRequest;
use Illuminate\Support\Facades\Input;
use Redis;

class LayerController extends BaseController
{
    /**
     * 用户产品动画层控制器
     */

    protected $rediskey;

    public function __construct()
    {
        parent::__construct();
        $this->rediskey = 'online_user_'.$this->userid.'_layer_';
    }

    public function index($pro_id,$layerid=0)
    {
        $apiProduct = ApiProduct::getOneByUid($pro_id,$this->userid);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        $proAttrArr = $apiProduct['data']['attr'] ? unserialize($apiProduct['data']['attr']) : [];
        $apiProLayer = ApiProLayer::index($pro_id);
        if ($apiProLayer['code']!=0) {
            echo "<script>alert('".$apiProLayer['msg']."');history.go(-1);</script>";exit;
        }
        //当前操作的动画层
        $layerid = $layerid ? $layerid : $apiProLayer['data'][0]['id'];
        $apiLayer = ApiProLayer::show($layerid);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('".$apiLayer['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'product'   =>  $apiProduct['data'],
            'datas'     =>  $apiProLayer['data'],
            'layerid'   =>  $layerid,
            'layerName'     =>  $apiLayer['data']['name'],
            'proAttrArr'    =>  $proAttrArr,
        ];
        return view('member.layer.index', $result);
    }

    public function show($pro_id,$id)
    {
        $apiProduct = ApiProduct::show($pro_id);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        if ($apiProduct['data']['attr']) { $proAttrArr = unserialize($apiProduct['data']['attr']); }
        //动画属性：先读缓存，在读数据表
        $apiProLayer = ApiProLayer::show($id);
        if ($apiProLayer['code']!=0) {
            echo "<script>alert('".$apiProLayer['msg']."');history.go(-1);</script>";exit;
        }
        if ($rstRedis=Redis::get($this->rediskey.$id)) {
            $layerArr = unserialize($rstRedis);
            $layers = isset($layerArr['layer'])?$layerArr['layer']:[];
            $attrs = isset($layerArr['attr'])?$layerArr['attr']:[];
            $cons = isset($layerArr['con'])?$layerArr['con']:[];
            $menutab = isset($layerArr['menu']['menutab'])?$layerArr['menu']['menutab']:1;
            $hasRedis = ($layers||$attrs||$cons) ? 1 : 0;      //判断是否有缓存
        }
        //动画、属性、内容，没有的话去接口获取
        $apilayer = $apiProLayer['data'];
        if (!isset($layers) || (isset($layers)&&!$layers)) {
            $layers = [
                'id' => $apilayer['id'],
                'name' => $apilayer['name'],
                'delay' => $apilayer['delay'],
                'timelong' => $apilayer['timelong'],
                'isshow' => $apilayer['isshow'],
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
        $result = [
            'product'   =>  $apiProduct['data'],
            'model' => $this->getModel(),
            'layers' => isset($layers) ? $layers : [],
            'attrs' => isset($attrs) ? $attrs : [],
            'cons' => isset($cons) ? $cons : [],
            'menutab' => isset($menutab) ? $menutab : 1,
            'hasRedis' => (isset($hasRedis)&&$hasRedis) ? 1 : 0,
            'proAttrArr' => isset($proAttrArr) ? $proAttrArr : [],
        ];
        return view('member.layer.iframe', $result);
    }

    /**
     * ajax更新动画设置
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
     * ajax更新动画属性
     */
    public function setAttr()
    {
        if (AjaxRequest::ajax()) {
            //验证宽高
            if (Input::get('width')!='' && floor(Input::get('width'))!=Input::get('width')) {
                echo json_encode(array('code'=>-1, 'msg'=>'宽度只能是正整数！'));exit;
            }
            if (Input::get('height')!='' && floor(Input::get('height'))!=Input::get('height')) {
                echo json_encode(array('code'=>-1, 'msg'=>'高度只能是正整数！'));exit;
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
                'isbigbg'      =>  Input::get('isbigbg'),
                'bigbg'        =>  Input::get('bigbg'),
            ];
            Redis::setex($rediskey,$this->redisTime,serialize($data));
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-2, 'msg'=>'参数错误！'));exit;
    }

    /**
     * ajax更新内容文字
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
            $data['con']['iscon'] = Input::get('iscon');
            $data['con']['text'] = Input::get('text');
            Redis::setex($rediskey,$this->redisTime,serialize($data));
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-2, 'msg'=>'参数错误！'));exit;
    }

    /**
     * 更新内容图片
     */
    public function setImg($pro_id,$id)
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
        $imgStr = $this->uploadOnlyImg(Input::all(),'url_ori',$oldImgArr);
        if (!$imgStr) {
            echo "<script>alert('图片上传有误！');history.go(-1);</script>";exit;
        }
        $data['con']['iscon'] = Input::get('iscon');
        $data['con']['img'] = $imgStr;
        Redis::setex($rediskey,$this->redisTime,serialize($data));
        return redirect(DOMAIN.'u/pro/'.$pro_id.'/layer');
    }

    /**
     * 清除redis中对应的动画数据
     */
    public function delRedis($pro_id,$layerid)
    {
        $rediskey = $this->rediskey.$layerid;
        if ($rstRedis=Redis::get($rediskey)) {
            Redis::del($rediskey);
            return redirect(DOMAIN.'u/pro/'.$pro_id.'/layer/'.$layerid);
        }
        echo "<script>alert('没有修改过或者修改数据已过期！');history.go(-1);</script>";exit;
    }

    /**
     * redis中对应动画数据入库
     */
    public function saveRedisToDB($pro_id,$layerid)
    {
        $rstLayer = ApiProLayer::show($layerid);
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
            'uid'       =>  $this->userid,
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
            'isbigbg'      =>  isset($layerArr['attr']['isbigbg'])?$layerArr['attr']['isbigbg']:'',
            'bigbg'        =>  isset($layerArr['attr']['bigbg'])?$layerArr['attr']['bigbg']:'',
        ];
        $rstLayer2 = ApiProLayer::modify($data);
        if ($rstLayer2['code']!=0) {
            echo "<script>alert('".$rstLayer2['msg']."');history.go(-1);</script>";exit;
        }
        Redis::del($this->rediskey.$layerid);
        return redirect(DOMAIN.'u/pro/'.$pro_id.'/layer/'.$layerid);
    }

    /**
     * 隐藏/显示动画层
     */
    public function setIsShow($pro_id,$layerid,$isshow)
    {
        if (!$pro_id || !$layerid || !$isshow) {
            echo "<script>alert('参数错误！');history.go(-1);</script>";exit;
        }
        $apiLayer = ApiProLayer::setIsShow($this->userid,$layerid,$isshow);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('".$apiLayer['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'u/pro/preview/'.$pro_id);
    }

    /**
     * 销毁动画层记录
     */
    public function setDelete($pro_id,$layerid)
    {
        if (!$pro_id || !$layerid) {
            echo "<script>alert('参数错误！');history.go(-1);</script>";exit;
        }
        //同时要销毁内容中的图片：表中、缓存
        $apiProLayer = ApiProLayer::show($layerid);
        if ($apiProLayer['code']!=0) {
            echo "<script>alert('".$apiProLayer['msg']."');history.go(-1);</script>";exit;
        }
        $apiPLCons = $apiProLayer['data']['con'] ? unserialize($apiProLayer['data']['con']) : [];
        if ($apiPLCons && $apiPLCons['iscon']==2 && $apiPLCons['img']) {
            if ($apiPLCons && $apiPLCons['iscon']==2 && $apiPLCons['img']) {
                $imgArr1 = explode('/',$apiPLCons['img']);
                if (mb_substr($imgArr1[0],0,4)=='http') {
                    unset($imgArr1[0]); unset($imgArr1[1]); unset($imgArr1[2]);
                    $img1 = implode('/',$imgArr1);
                } else {
                    $img1 = ltrim($apiPLCons['img'],'/');
                }
                if (file_exists($img1)) { unlink($img1); }
            }
        }
        if ($rstRedis=Redis::get($this->rediskey.$layerid)) {
            $layerArr = unserialize($rstRedis);
            $cons = (isset($layerArr['con'])&&$layerArr['con']) ? $layerArr['con'] : [];
            if ($cons && $cons['iscon']==2 && $cons['img']) {
                $imgArr = explode('/',$cons['img']);
                if (mb_substr($imgArr[0],0,4)=='http') {
                    unset($imgArr[0]); unset($imgArr[1]); unset($imgArr[2]);
                    $img = implode('/',$imgArr);
                } else {
                    $img = ltrim($cons['img'],'/');
                }
                if (file_exists($img)) { unlink($img); }
            }
        }
        $apiLayer = ApiProLayer::delete($this->userid,$layerid);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('".$apiLayer['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'u/pro/preview/'.$pro_id);
    }

    /**
     * 获取 model
     */
    public function getModel()
    {
        $rst = ApiProLayer::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
    }
}