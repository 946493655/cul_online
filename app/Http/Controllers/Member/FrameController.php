<?php
namespace App\Http\Controllers\Member;

use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiProFrame;
use App\Api\ApiOnline\ApiProLayer;

class FrameController extends BaseController
{
    /**
     * 产品关键帧
     */

    /**
     * 单个动画层预览
     */
    public function getPreLayer($pro_id,$layerid)
    {
        $apiProduct = ApiProduct::getOneByUid($pro_id,$this->userid);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        $apiProLayer = ApiProLayer::show($layerid);
        if ($apiProLayer['code']!=0) {
            echo "<script>alert('".$apiProLayer['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'product'   =>  $apiProduct['data'],
            'layer'     =>  $apiProLayer['data'],
        ];
        return view('member.frame.onelayer', $result);
    }

    /**
     * 载入动画框架
     */
    public function getKeyVals($pro_id,$layerid)
    {
        $apiProLayer = ApiProLayer::show($layerid);
        if ($apiProLayer['code']!=0) {
            echo "<script>alert('".$apiProLayer['msg']."');history.go(-1);</script>";exit;
        }
        if ($apiProLayer['data']['con']) { $cons = unserialize($apiProLayer['data']['con']); }
        if ($apiProLayer['data']['attr']) { $attrs = unserialize($apiProLayer['data']['attr']); }
        $apiFrameLeft = ApiProFrame::index($pro_id,$layerid,1);
        $apiFrameTop = ApiProFrame::index($pro_id,$layerid,2);
        $apiFrameOpacity = ApiProFrame::index($pro_id,$layerid,3);
        $apiFrameRotate = ApiProFrame::index($pro_id,$layerid,4);
        $apiFrameScale = ApiProFrame::index($pro_id,$layerid,5);
        $result = [
            'layer' =>  $apiProLayer['data'],
            'cons'      =>  isset($cons) ? $cons : [],
            'attrs'     =>  isset($attrs) ? $attrs : [],
            'layerModel'    =>  $this->getLayerModel(),
            'frameLeft'     =>  $apiFrameLeft['code']==0 ? $apiFrameLeft['data'] : [],
            'frameTop'      =>  $apiFrameTop['code']==0 ? $apiFrameTop['data'] : [],
            'frameOpacity'  =>  $apiFrameOpacity['code']==0 ? $apiFrameOpacity['data'] : [],
            'frameRotate'   =>  $apiFrameRotate['code']==0 ? $apiFrameRotate['data'] : [],
            'frameScale'    =>  $apiFrameScale['code']==0 ? $apiFrameScale['data'] : [],
        ];
        return view('member.frame.keyval', $result);
    }

    /**
     * 获取 layerModel
     */
    public function getLayerModel()
    {
        $rst = ApiProLayer::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
    }
}