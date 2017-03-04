<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiTempFrame;
use App\Api\ApiOnline\ApiTempLayer;
use App\Api\ApiOnline\ApiTempPro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as AjaxRequest;
use Illuminate\Support\Facades\Input;
use Redis;

class TFrameController extends BaseController
{
    /**
     * 关键帧
     */

    protected $keyRedis = 'online_admin_temp_layer_';

    /**
     * 通过 id 获取关键帧模板
     */
    public function index($tempid,$layerid)
    {
//        if (!$tempid || !$layerid) {
//            echo "<script>alert('参数错误！');history.go(-1);</script>";exit;
//        }
        //先查看缓存中有没有关键帧数据
        $attr = 1;
        $rstRedis = Redis::get($this->keyRedis.$layerid);
        if ($rstRedis) {
            $layerArr = unserialize($rstRedis);
            $attr = (isset($layerArr['menu']['attrtab'])&&$layerArr['menu']['attrtab']) ?
                $layerArr['menu']['attrtab'] : $attr;
            if ($attr==1 && isset($layerArr['frame_left'])) { $leftArr = $layerArr['frame_left']; }
            if ($attr==2 && isset($layerArr['frame_top'])) { $topArr = $layerArr['frame_top']; }
            if ($attr==3 && isset($layerArr['frame_opacity'])) { $opacityArr = $layerArr['frame_opacity']; }
            if ($attr==4 && isset($layerArr['frame_rotate'])) { $rotateArr = $layerArr['frame_rotate']; }
            if ($attr==5 && isset($layerArr['frame_scale'])) { $scaleArr = $layerArr['frame_scale']; }
            if (isset($layerArr['menu']['hasframe']) && $layerArr['menu']['hasframe']) {
                $frameRedis = 1;
            }
        }
        //没有，再查询数据表
        if (!isset($leftArr)) {
            $apiFrameLefts = ApiTempFrame::index($tempid,$layerid,$attr);
            $leftArr = $apiFrameLefts['code']==0 ? $apiFrameLefts['data'] : [];
            $layerArr['frame_left'] = $leftArr;
        }
        if (!isset($topArr)) {
            $apiFrameTops = ApiTempFrame::index($tempid,$layerid,$attr);
            $topArr = $apiFrameTops['code']==0 ? $apiFrameTops['data'] : [];
            $layerArr['frame_top'] = $topArr;
        }
        if (!isset($opacityArr)) {
            $apiFrameOpacitys = ApiTempFrame::index($tempid,$layerid,$attr);
            $opacityArr = $apiFrameOpacitys['code']==0 ? $apiFrameOpacitys['data'] : [];
            $layerArr['frame_opacity'] = $opacityArr;
        }
        if (!isset($rotateArr)) {
            $apiFrameRotates = ApiTempFrame::index($tempid,$layerid,$attr);
            $rotateArr = $apiFrameRotates['code']==0 ? $apiFrameRotates['data'] : [];
            $layerArr['frame_rotate'] = $rotateArr;
        }
        if (!isset($scaleArr)) {
            $apiFrameScales = ApiTempFrame::index($tempid,$layerid,$attr);
            $scaleArr = $apiFrameScales['code']==0 ? $apiFrameScales['data'] : [];
            $layerArr['frame_rotate'] = $scaleArr;
        }
        //获取model
        $apiTempFrameModel = ApiTempFrame::getModel();
        //保存到缓存
        if (!$rstRedis) {
            $layerArr['menu']['hasframe'] = isset($frameRedis) ? 1 : 0;
            Redis::setex($this->keyRedis.$layerid,$this->redisTime,serialize($layerArr));
        }
        $result = [
            'leftArr' => isset($leftArr) ? $leftArr : [],
            'topArr' => isset($topArr) ? $topArr : [],
            'opacityArr' => isset($opacityArr) ? $opacityArr : [],
            'rotateArr' => isset($rotateArr) ? $rotateArr : [],
            'scaleArr' => isset($scaleArr) ? $scaleArr : [],
            'model' => $apiTempFrameModel['code']==0 ? $apiTempFrameModel['model'] : [],
            'tempid' => $tempid,
            'layerid' => $layerid,
            'attr' => $attr,
            'frameRedis' => isset($frameRedis) ? 1 : 0,
        ];
        return view('admin.frame.index',$result);
    }

    public function store(Request $request,$tempid,$layerid)
    {
        if (!$tempid || !$layerid) {
            echo "<script>alert('暂无动画层！');history.go(-1);</script>";exit;
        }
        $data = $this->getData($request);
        $apiKey = ApiTempFrame::add($data);
        if ($apiKey['code']!=0) {
            echo "<script>alert('".$apiKey['msg']."');history.go(-1);</script>";exit;
        }
        //假如这里有数据，并且有缓存，则新加的数据加入缓存
        $rstFrame = ApiTempFrame::index($request->tempid,$request->layerid,$request->selattr);
        $rstRedis = Redis::get($this->keyRedis.$request->layerid);
        if ($rstRedis && $rstFrame['code']==0 && count($rstFrame['data'])>1) {
            $layerArr = unserialize($rstRedis);
            if ($request->selattr==1) {
                $layerArr['frame_left'][$apiKey['data']['id']] = $apiKey['data'];
            } elseif ($request->selattr==2) {
                $layerArr['frame_top'][$apiKey['data']['id']] = $apiKey['data'];
            } elseif ($request->selattr==3) {
                $layerArr['frame_opacity'][$apiKey['data']['id']] = $apiKey['data'];
            } elseif ($request->selattr==4) {
                $layerArr['frame_rotate'][$apiKey['data']['id']] = $apiKey['data'];
            } elseif ($request->selattr==5) {
                $layerArr['frame_scale'][$apiKey['data']['id']] = $apiKey['data'];
            }
            Redis::setex($this->keyRedis.$request->layerid,$this->redisTime,serialize($layerArr));
        }
        return redirect(DOMAIN.'admin/t/'.$data['tempid'].'/'.$data['layerid'].'/frame');
    }

    /**
     * 选择当前做动画的属性
     */
    public function selAttr()
    {
        if (AjaxRequest::ajax()) {
            $data = Input::all();
            if (!$data['tempid'] || !$data['layerid'] || !$data['attr']) {
                echo json_encode(array('code'=>-2, 'msg'=>'参数错误！'));
            }
            $rstRedis = Redis::get($this->keyRedis.$data['layerid']);
            if ($rstRedis) {
                $layerArr = unserialize($rstRedis);
            }
            $layerArr['menu']['attrtab'] = $data['attr'];
            Redis::setex($this->keyRedis.$data['layerid'],$this->redisTime,serialize($layerArr));
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-1, 'msg'=>'参数错误！'));exit;
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
            //假如有缓存，更新缓存
            if ($attr==1) {
                $frame_key = 'frame_left';
            } elseif ($attr==2) {
                $frame_key = 'frame_top';
            } elseif ($attr==3) {
                $frame_key = 'frame_opacity';
            } elseif ($attr==4) {
                $frame_key = 'frame_rotate';
            } elseif ($attr==5) {
                $frame_key = 'frame_scale';
            }
            $rstRedis = Redis::get($this->keyRedis.$layerid);
            if ($rstRedis) {
                $layerArr = unserialize($rstRedis);
            }
            $layerArr[$frame_key][$frameid]['per'] = $per;
            $layerArr[$frame_key][$frameid]['val'] = $val;
            $layerArr['menu']['hasframe'] = 1;      //代表有修改但未保存
            Redis::setex($this->keyRedis.$layerid,$this->redisTime,serialize($layerArr));
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-1, 'msg'=>'参数错误！'));exit;
    }

    /**
     * 取消缓存
     */
    public function delRedis($tempid,$layerid)
    {
        if (!$tempid || !$layerid) {
            echo "<script>alert('参数有误！');history.go(-1);</script>";exit;
        }
        $keyRedis = $this->keyRedis.$layerid;
        if (Redis::get($keyRedis)) { Redis::del($keyRedis); }
        return redirect(DOMAIN.'admin/t/'.$tempid.'/'.$layerid.'/frame');
    }

    /**
     * 保存缓存到数据库
     */
    public function saveRedisToDB($tempid,$layerid)
    {
        if (!$tempid || !$layerid) {
            echo "<script>alert('参数有误！');history.go(-1);</script>";exit;
        }
        $keyRedis = $this->keyRedis.$layerid;
        if ($rstRedis=Redis::get($keyRedis)) {
            $layerArr = unserialize($rstRedis);
            $datas = array();
            if (isset($layerArr['frame_left']) && $frameLeft=$layerArr['frame_left']) {
                foreach ($frameLeft as $kleft=>$left) {
                    $leftArr = [
                        'id'    =>  $kleft,
                        'per'   =>  $left['per'],
                        'val'   =>  $left['val'],
                    ];
                    $datas['frames'][$kleft] = $leftArr;
                    $datas['frameids'][] = $kleft;
                }
            }
            if (isset($layerArr['frame_top']) && $frameTop=$layerArr['frame_top']) {
                foreach ($frameTop as $ktop=>$top) {
                    $topArr = [
                        'id'    =>  $ktop,
                        'per'   =>  $top['per'],
                        'val'   =>  $top['val'],
                    ];
                    $datas['frames'][$ktop] = $topArr;
                    $datas['frameids'][] = $ktop;
                }
            }
            if (isset($layerArr['frame_opacity']) && $frameOpacity=$layerArr['frame_opacity']) {
                foreach ($frameOpacity as $kopacity=>$opacity) {
                    $opacityArr = [
                        'id'    =>  $kopacity,
                        'per'   =>  $opacity['per'],
                        'val'   =>  $opacity['val'],
                    ];
                    $datas['frames'][$kopacity] = $opacityArr;
                    $datas['frameids'][] = $kopacity;
                }
            }
            if (isset($layerArr['frame_rotate']) && $frameRotate=$layerArr['frame_rotate']) {
                foreach ($frameRotate as $krotate=>$rotate) {
                    $rotateArr = [
                        'id'    =>  $krotate,
                        'per'   =>  $rotate['per'],
                        'val'   =>  $rotate['val'],
                    ];
                    $datas['frames'][$krotate] = $rotateArr;
                    $datas['frameids'][] = $krotate;
                }
            }
            if (isset($layerArr['frame_scale']) && $frameScale=$layerArr['frame_scale']) {
                foreach ($frameScale as $kscale=>$scale) {
                    $scaleArr = [
                        'id'    =>  $kscale,
                        'per'   =>  $scale['per'],
                        'val'   =>  $scale['val'],
                    ];
                    $datas['frames'][$kscale] = $scaleArr;
                    $datas['frameids'][] = $kscale;
                }
            }
            $rstFrame = ApiTempFrame::modify($datas);
            if ($rstFrame['code']!=0) {
                echo "<script>alert('".$rstFrame['msg']."');history.go(-1);</script>";exit;
            }
        }
        return redirect(DOMAIN.'admin/t/'.$tempid.'/'.$layerid.'/frame');
    }

    /**
     * ajax 删除关键帧
     */
    public function delete($tempid,$layerid)
    {
        if (AjaxRequest::ajax()) {
            $data = Input::all();
            if (!$data['id'] || !$data['attr']) {
                echo json_encode(array('code'=>-2, 'msg'=>'参数有误！'));exit;
            }
            $rstFrame = ApiTempFrame::forceDelete($data['id']);
            if ($rstFrame['code']!=0) {
                echo json_encode(array('code'=>-3, 'msg'=>$rstFrame['msg']));exit;
            }
            //删除缓存中类似关键帧
            $keyRedis = $this->keyRedis.$data['layerid'];
            if ($rstRedis=Redis::get($keyRedis)) {
                $layerArr = unserialize($rstRedis);
                if (isset($layerArr['frame_left']) && $data['attr']==1 && array_key_exists($data['id'],$layerArr['frame_left'])) {
                    unset($layerArr['frame_left'][$data['id']]);
                } elseif (isset($layerArr['frame_top']) && $data['attr']==1 && array_key_exists($data['id'],$layerArr['frame_top'])) {
                    unset($layerArr['frame_top'][$data['id']]);
                } elseif (isset($layerArr['frame_opacity']) && $data['attr']==1 && array_key_exists($data['id'],$layerArr['frame_opacity'])) {
                    unset($layerArr['frame_opacity'][$data['id']]);
                } elseif (isset($layerArr['frame_rotate']) && $data['attr']==1 && array_key_exists($data['id'],$layerArr['frame_rotate'])) {
                    unset($layerArr['frame_rotate'][$data['id']]);
                } elseif (isset($layerArr['frame_scale']) && $data['attr']==1 && array_key_exists($data['id'],$layerArr['frame_scale'])) {
                    unset($layerArr['frame_scale'][$data['id']]);
                }
                Redis::setex($this->keyRedis.$data['layerid'],$this->redisTime,serialize($layerArr));
            }
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-1, 'msg'=>'数据错误！'));exit;
    }

    /**
     * 预览一个动画层
     */
    public function getPreLayer($tempid,$layerid)
    {
        if (!$tempid || !$layerid) {
            echo "<script>alert('层参数有误！');history.go(-1);</script>";exit;
        }
        $apiLayer = ApiTempLayer::show($layerid);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('".$apiLayer['msg']."');history.go(-1);</script>";exit;
        }
        $apiFrame = ApiTempFrame::index($tempid,$layerid,0);
        if ($apiFrame['code']!=0) {
            echo "<script>alert('没有动画关键帧！');history.go(-1);</script>";exit;
        }
        $result = [
            'layerModel' => $apiLayer['data'],
            'tempid' => $tempid,
            'layerid' => $layerid,
        ];
        return view('admin.frame.onelayer', $result);
    }

    /**
     * 动画层的key-value载入
     */
    public function getKeyVals($tempid,$layerid)
    {
        $apiLayer = ApiTempLayer::show($layerid);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('".$apiLayer['msg']."');history.go(-1);</script>";exit;
        }
        if ($apiLayer['data']['attr']) {
            $attrs = unserialize($apiLayer['data']['attr']);
        }
        if ($apiLayer['data']['con']) {
            $cons = unserialize($apiLayer['data']['con']);
        }
        $apiFrameLeft = ApiTempFrame::index($tempid,$layerid,1);
        $apiFrameTop = ApiTempFrame::index($tempid,$layerid,2);
        $apiFrameOpacity = ApiTempFrame::index($tempid,$layerid,3);
        $apiFrameRotate = ApiTempFrame::index($tempid,$layerid,4);
        $apiFrameScale = ApiTempFrame::index($tempid,$layerid,5);
        $result = [
            'layer' => $apiLayer['data'],
            'layerModel' => $this->getLayerModel(),
            'attrs' => isset($attrs) ? $attrs : [],
            'cons' => isset($cons) ? $cons : [],
            'frameLeft' => $apiFrameLeft['code']==0 ? $apiFrameLeft['data'] : [],
            'frameTop' => $apiFrameTop['code']==0 ? $apiFrameTop['data'] : [],
            'frameOpacity' => $apiFrameOpacity['code']==0 ? $apiFrameOpacity['data'] : [],
            'frameRotate' => $apiFrameScale['code']==0 ? $apiFrameScale['data'] : [],
        ];
        return view('admin.frame.keyval',$result);
    }





    public function getData(Request $request)
    {
        return array(
            'tempid'    =>  $request->tempid,
            'layerid'   =>  $request->layerid,
            'attr'      =>  $request->selattr,
            'per'       =>  $request->per,
            'val'       =>  $request->val,
        );
    }

    /**
     * 获取 layerModel
     */
    public function getLayerModel()
    {
        $rst = ApiTempLayer::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
    }
}