<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiProLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as AjaxRequest;
use Illuminate\Support\Facades\Input;

class ProLayerController extends BaseController
{
    /**
     * 后台产品动画层
     */

    public function index($pro_id,$layerid=0)
    {
        $apiProduct = ApiProduct::show($pro_id);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        $proAttrArr = $apiProduct['data']['attr'] ? unserialize($apiProduct['data']['attr']) : [];
        $apiProLayer = ApiProLayer::index($pro_id);
        //当前操作的动画层
        if (!$layerid) {
            $layerid = $apiProLayer['code']==0 ? $apiProLayer['data'][0]['id'] : 0;
        }
        $rstLayer = ApiProLayer::show($pro_id);
        $result = [
            'product' => $apiProduct['data'],
            'datas' => $apiProLayer['code']==0 ? $apiProLayer['data'] : [],
            'layerid' => $layerid,
            'layerName' => $rstLayer['code']==0 ? $rstLayer['data']['name'] : '',
            'proAttrArr' => $proAttrArr,
        ];
        return view('admin.product.layer.index',$result);
    }

    public function show($pro_id,$id)
    {
        $apiProduct = ApiProduct::show($pro_id);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        if ($apiProduct['data']['attr']) { $proAttrArr = unserialize($apiProduct['data']['attr']); }
        $apiProLayer = ApiProLayer::show($id);
        $apilayer = $apiProLayer['code']==0 ? $apiProLayer['data']: [];
        if ($apilayer) {
            $layers = [
                'id' => $apilayer['id'],
                'name' => $apilayer['name'],
                'delay' => $apilayer['delay'],
                'timelong' => $apilayer['timelong'],
                'isshow' => $apilayer['isshow'],
            ];
            $attrs = $apilayer['attr'] ? unserialize($apilayer['attr']) : [];
            $conArr = $apilayer['con'] ? unserialize($apilayer['con']) : [];
            $cons = [
                'iscon' =>  $conArr ? $conArr['iscon'] : [],
                'text'  =>  $conArr ? $conArr['text'] : [],
                'img'   =>  $conArr ? $conArr['img'] : [],
            ];
        }
        $result = [
            'product' => $apiProduct['data'],
            'model' => $this->getModel(),
            'layers' => isset($layers) ? $layers : [],
            'attrs' => isset($attrs) ? $attrs : [],
            'cons' => isset($cons) ? $cons : [],
//            'menutab' => isset($menutab) ? $menutab : 1,
//            'hasRedis' => (isset($hasRedis)&&$hasRedis) ? 1 : 0,
            'proAttrArr' => isset($proAttrArr) ? $proAttrArr : [],
        ];
        return view('admin.product.layer.iframe',$result);
    }

    /**
     * 添加动画层
     */
    public function store(Request $request,$pro_id)
    {
        $data = [
            'name'  =>  $request->name,
            'pro_id'    =>  $request->pro_id,
            'delay'     =>  $request->delay,
            'timelong'  =>  $request->timelong,
        ];
        $apiProLayer = ApiProLayer::add($data);
        if ($apiProLayer['code']!=0) {
            echo "<script>alert('".$apiProLayer['data']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/pro/'.$pro_id.'/layer');
    }

    /**
     * 动画设置
     */
    public function setLayer(Request $request)
    {
        if (AjaxRequest::ajax()) {
            if (!$request->name || !$request->timelong) {
                echo json_encode(array('code'=>-1, 'msg'=>'数据不对！'));exit;
            }
            $id = $request->layerid;
            $data = [
                'name'  =>  $request->name,
                'delay' =>  $request->delay,
                'timelong'  =>  $request->timelong,
            ];
            if (!$this->saveToDB($id,$data,array(),'','')) {
                echo json_encode(array('code'=>-3, 'msg'=>'操作失败！'));exit;
            }
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-2, 'msg'=>'参数错误！'));exit;
    }

    /**
     * 属性设置
     */
    public function setAttr(Request $request)
    {
    }

    /**
     * 文字设置
     */
    public function setText(Request $request)
    {
    }

    /**
     * 图片设置
     */
    public function setImg(Request $request)
    {
    }

    /**
     * 动画数据入库
     */
    public function saveToDB($id,$layers=array(),$attrs=array(),$text='',$img='')
    {
        if (!$id && !$layers && !$attrs && !$text && !$img) { return false; }
        $apiProLayer = ApiProLayer::show($id);
        $apiProduct = ApiProduct::show($apiProLayer['data']['pro_id']);
        if (!$attrs) {
            $attrs = $apiProLayer['data']['attr'] ? unserialize($apiProLayer['data']['attr']) : [];
        }
        if ($text) { $iscon = 1; } else if ($img) { $iscon = 2; }
        $data = [
            'id'        =>  $id,
            'uid'       =>  $apiProduct['data']['uid'],
            'name'      =>  $layers ? $layers['name'] : $apiProLayer['data']['name'],
            'delay'     =>  $layers ? $layers['delay'] : $apiProLayer['data']['delay'],
            'timelong'  =>  $layers ? $layers['timelong'] : $apiProLayer['data']['timelong'],
            'width'     =>  $attrs ? $attrs['width'] : 300,
            'height'    =>  $attrs ? $attrs['height'] : 100,
            'isborder'  =>  $attrs ? $attrs['isborder'] : 1,
            'border1'   =>  $attrs ? $attrs['border1'] : 1,
            'border2'   =>  $attrs ? $attrs['border2'] : 1,
            'border3'   =>  $attrs ? $attrs['border3'] : '#ff0000',
            'isbg'      =>  $attrs ? $attrs['isbg'] : 1,
            'bg'        =>  $attrs ? $attrs['bg'] : '#ffffff',
            'iscolor'   =>  $attrs ? $attrs['iscolor'] : 0,
            'color'     =>  $attrs ? $attrs['color'] : '000000',
            'fontsize'  =>  $attrs ? $attrs['fontsize'] : 16,
            'isbigbg'   =>  $attrs ? $attrs['isbigbg'] : 0,
            'bigbg'     =>  $attrs ? $attrs['bigbg'] : '#9a9a9a',
            'iscon'     =>  isset($iscon) ? $iscon : 1,
            'text'      =>  $text,
            'img'       =>  $img,
        ];
        $apiProLayer = ApiProLayer::modify($data);
        if ($apiProLayer['code']!=0) {
            echo "<script>alert('".$apiProLayer['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/pro/'.$apiProLayer['data']['pro_id'].'/layer');
    }

    /**
     * 获取 model
     */
    public function getModel()
    {
        $apiModel = ApiProLayer::getModel();
        return $apiModel['code']==0 ? $apiModel['model'] : [];
    }
}