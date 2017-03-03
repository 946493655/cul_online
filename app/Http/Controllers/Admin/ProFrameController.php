<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiProFrame;

class ProFrameController extends BaseController
{
    /**
     * 后台产品关键帧
     */

    public function index($pro_id,$layerid)
    {
        $attr = 1;
        //水平关键帧
        $apiLeft = ApiProFrame::index($pro_id,$layerid,$attr);
        $leftArr = $apiLeft['code']==0 ? $apiLeft['data'] : [];
        $layerArr['frame_left'] = $leftArr;
        //垂直关键帧
        $apiTop = ApiProFrame::index($pro_id,$layerid,$attr);
        $topArr = $apiTop['code']==0 ? $apiTop['data'] : [];
        $layerArr['frame_top'] = $topArr;
        //透明度关键帧
        $apiOpacity = ApiProFrame::index($pro_id,$layerid,$attr);
        $opacityArr = $apiOpacity['code']==0 ? $apiOpacity['data'] : [];
        $layerArr['frame_opacity'] = $opacityArr;
        //旋转关键帧
        $apiRotate = ApiProFrame::index($pro_id,$layerid,$attr);
        $rotateArr = $apiRotate['code']==0 ? $apiRotate['data'] : [];
        $layerArr['frame_rotate'] = $rotateArr;
        //缩放关键帧
        $apiScale = ApiProFrame::index($pro_id,$layerid,$attr);
        $scaleArr = $apiScale['code']==0 ? $apiScale['data'] : [];
        $layerArr['frame_scale'] = $scaleArr;
        $result = [
            'leftArr' => isset($leftArr) ? $leftArr : [],
            'topArr' => isset($topArr) ? $topArr : [],
            'opacityArr' => isset($opacityArr) ? $opacityArr : [],
            'rotateArr' => isset($rotateArr) ? $rotateArr : [],
            'scaleArr' => isset($scaleArr) ? $scaleArr : [],
            'model' => $this->getModel(),
            'pro_id' => $pro_id,
            'layerid' => $layerid,
            'attr' => $attr,
            'frameRedis' => isset($frameRedis) ? 1 : 0,
        ];
        return view('admin.product.frame.index',$result);
    }

    /**
     * 获取 model
     */
    public function getModel()
    {
        $apiModel = ApiProFrame::getModel();
        return $apiModel['code']==0 ? $apiModel['model'] : [];
    }
}