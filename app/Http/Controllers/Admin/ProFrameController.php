<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiProFrame;
use App\Api\ApiOnline\ApiProLayer;
use Illuminate\Support\Facades\Request as AjaxRequest;
use Illuminate\Support\Facades\Input;

class ProFrameController extends BaseController
{
    /**
     * 后台产品关键帧
     */

    protected $keyRedis = 'online_admin_pro_layer_';

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
     * 根据 id 更新关键帧的 百分比per、值val
     */
    public function setKeyVal()
    {
        if (AjaxRequest::ajax()) {
            $layerid = Input::get('layerid');
            $frameid = Input::get('frameid');
            $attr = Input::get('attr');
            $per = Input::get('per');
            $val = Input::get('val');
            if (!$layerid || !$frameid || !in_array($attr,[1,2,3,4,5])) {
                echo json_encode(array('code'=>-2, 'msg'=>'参数错误！'));
            }
            if (floor($per)!=$per || $per<0 || $per>100) {
                echo json_encode(array('code'=>-3, 'msg'=>'百分比必须是 0-100 的整数！'));
            }
            if (in_array($attr,[1,2]) && (floor($val)!=$val)) {
                echo json_encode(array('code'=>-4, 'msg'=>'距离必须是整数！'));
            } elseif ($attr==3 && (floor($val)!=$val||$val<0||$val>100)) {
                echo json_encode(array('code'=>-4, 'msg'=>'透明度必须是 0-100 的整数！'));
            }
            if (in_array($attr,[4,5]) && floor($val)!=$val) {
                echo json_encode(array('code'=>-3, 'msg'=>'旋转或缩放必须是整数！'));
            }
            $data = [
                'id'    =>  $frameid,
                'attr'  =>  $attr,
                'per'   =>  $per,
                'val'   =>  $val,
            ];
            $apiFrame = ApiProFrame::modify($data);
            if ($apiFrame['code']!=0) {
                echo "<script>alert('".$apiFrame['msg']."');history.go(-1);</script>";exit;
            }
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-1, 'msg'=>'参数错误！'));exit;
    }

    /**
     * 预览一个动画层
     */
    public function getPreLayer($pro_id,$layerid)
    {
        if (!$pro_id || !$layerid) {
            echo "<script>alert('层参数有误！');history.go(-1);</script>";exit;
        }
        $apiLayer = ApiProLayer::show($pro_id);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('".$apiLayer['msg']."');history.go(-1);</script>";exit;
        }
        $apiFrame = ApiProFrame::index($pro_id,$layerid,0);
        if ($apiFrame['code']!=0) {
            echo "<script>alert('没有动画关键帧！');history.go(-1);</script>";exit;
        }
        $result = [
            'layerModel' => $apiLayer['data'],
            'pro_id' => $pro_id,
            'layerid' => $layerid,
        ];
        return view('admin.product.frame.onelayer', $result);
    }

    /**
     * 动画层的key-value载入
     */
    public function getKeyVals($pro_id,$layerid)
    {
        $apiLayer = ApiProLayer::show($pro_id);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('".$apiLayer['msg']."');history.go(-1);</script>";exit;
        }
        if ($apiLayer['data']['attr']) {
            $attrs = unserialize($apiLayer['data']['attr']);
        }
        if ($apiLayer['data']['con']) {
            $cons = unserialize($apiLayer['data']['con']);
        }
        $apiFrameLeft = ApiProFrame::index($pro_id,$layerid,1);
        $apiFrameTop = ApiProFrame::index($pro_id,$layerid,2);
        $apiFrameOpacity = ApiProFrame::index($pro_id,$layerid,3);
        $apiFrameRotate = ApiProFrame::index($pro_id,$layerid,4);
        $apiFrameScale = ApiProFrame::index($pro_id,$layerid,5);
        $result = [
            'layer' => $apiLayer['data'],
            'layerModel' => $this->getLayerModel(),
            'attrs' => isset($attrs) ? $attrs : [],
            'cons' => isset($cons) ? $cons : [],
            'frameLeft' => $apiFrameLeft['code']==0 ? $apiFrameLeft['data'] : [],
            'frameTop' => $apiFrameTop['code']==0 ? $apiFrameTop['data'] : [],
            'frameOpacity' => $apiFrameOpacity['code']==0 ? $apiFrameOpacity['data'] : [],
            'frameRotate' => $apiFrameRotate['code']==0 ? $apiFrameRotate['data'] : [],
            'frameScale' => $apiFrameScale['code']==0 ? $apiFrameScale['data'] : [],
        ];
        return view('admin.product.frame.keyval',$result);
    }

    /**
     * 获取 model
     */
    public function getModel()
    {
        $apiModel = ApiProFrame::getModel();
        return $apiModel['code']==0 ? $apiModel['model'] : [];
    }

    /**
     * 获取 layerModel
     */
    public function getLayerModel()
    {
        $apiModel = ApiProLayer::getModel();
        return $apiModel['code']==0 ? $apiModel['model'] : [];
    }
}