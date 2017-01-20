<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiTempFrame;
use App\Api\ApiOnline\ApiTempLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as AjaxRequest;
use Illuminate\Support\Facades\Input;
use Redis;

class FrameController extends BaseController
{
    /**
     * 关键帧
     */

    protected $keyRedis = 'online_admin_layer_';

    /**
     * 通过 id 获取关键帧模板
     */
    public function index($tempid,$layerid)
    {
        if (!$tempid || !$layerid) {
            echo "<script>alert('参数错误！');history.go(-1);</script>";exit;
        }
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
            if (isset($layerArr['menu']['hasframe']) && $layerArr['menu']['hasframe']) {
                $frameRedis = 1;
            }
        }
        //没有，再查询数据表
        if ($attr==1 && !isset($leftArr)) {
            $apiFrameLefts = ApiTempFrame::index($tempid,$layerid,$attr);
            $leftArr = $apiFrameLefts['code']==0 ? $apiFrameLefts['data'] : [];
            $layerArr['frame_left'] = $leftArr;
        }
        if ($attr==2 && !isset($topArr)) {
            $apiFrameTops = ApiTempFrame::index($tempid,$layerid,$attr);
            $topArr = $apiFrameTops['code']==0 ? $apiFrameTops['data'] : [];
            $layerArr['frame_top'] = $topArr;
        }
        if ($attr==3 && !isset($opacityArr)) {
            $apiFrameOpacitys = ApiTempFrame::index($tempid,$layerid,$attr);
            $opacityArr = $apiFrameOpacitys['code']==0 ? $apiFrameOpacitys['data'] : [];
            $layerArr['frame_opacity'] = $opacityArr;
        }
        //获取model
        $apiTempFrameModel = ApiTempFrame::getModel();
        //保存到缓存
//        dd(unserialize(Redis::get($this->keyRedis.$layerid)));
        if (!$rstRedis) {
            $layerArr['menu']['hasframe'] = isset($frameRedis) ? 1 : 0;
            Redis::setex($this->keyRedis.$layerid,$this->redisTime,serialize($layerArr));
        }
        $result = [
            'leftArr' => isset($leftArr) ? $leftArr : [],
            'topArr' => isset($topArr) ? $topArr : [],
            'opacityArr' => isset($opacityArr) ? $opacityArr : [],
            'model' => $apiTempFrameModel['code']==0 ? $apiTempFrameModel['model'] : [],
            'tempid' => $tempid,
            'layerid' => $layerid,
            'attr' => $attr,
            'frameRedis' => isset($frameRedis) ? 1 : 0,
        ];
        return view('admin.frame.index',$result);
    }

    public function store(Request $request)
    {
        $data = $this->getData($request);
        $apiKey = ApiTempFrame::add($data);
        if ($apiKey['code']!=0) {
            echo "<script>alert('".$apiKey['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/t/'.$data['tempid'].'/layer');
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
            if (!$layerid || !$frameid || !in_array($attr,[1,2,3])) {
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
            //假如有缓存，更新缓存
            if ($attr==1) {
                $frame_key = 'frame_left';
            } elseif ($attr==2) {
                $frame_key = 'frame_top';
            } else {
                $frame_key = 'frame_opacity';
            }
            $rstRedis = Redis::get($this->keyRedis.$layerid);
            if ($rstRedis) {
                $layerArr = unserialize($rstRedis);
            }
            $layerArr[$frame_key][$frameid]['per'] = $per;
            $layerArr[$frame_key][$frameid]['val'] = $val;
            $layerArr['menu']['hasframe'] = 1;      //代表有修改但未保存
//            dd(unserialize(Redis::get($this->keyRedis.$layerid)));
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
            $rstFrame = ApiTempFrame::modify($datas);
            if ($rstFrame['code']!=0) {
                echo "<script>alert('".$rstFrame['msg']."');history.go(-1);</script>";exit;
            }
        }
        return redirect(DOMAIN.'admin/t/'.$tempid.'/'.$layerid.'/frame');
    }

    /**
     * 预览一个动画层
     */
    public function getPreLayer($tempid,$layerid)
    {
        $apiLayer = ApiTempFrame::index($tempid,$layerid,0);
        if (!$tempid) {}
        $result = [];
        return view('admin.frame.onelayer');
    }

    /**
     * 动画层的key-value载入
     */
    public function getKeyVals($tempid,$layerid)
    {
        $result = [];
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
}