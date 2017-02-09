<?php
namespace App\Http\Controllers\Member;

use App\Api\ApiOnline\ApiOrder;
use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiProFrame;
use App\Api\ApiOnline\ApiProLayer;
use App\Api\ApiOnline\ApiTempFrame;
use App\Api\ApiOnline\ApiTempPro;
use Illuminate\Http\Request;
use Session;
use Redis;

class HomeController extends BaseController
{
    /**
     * 用户创作管理控制器
     */

    public function __construct()
    {
        parent::__construct();
    }

    public function index($cate=0)
    {
        $pageCurr = isset($_POST['pageCurr'])?$_POST['pageCurr']:1;
        $datas = $this->query($pageCurr,$cate);
        $pagelist = $this->getPageList($datas,DOMAIN,$this->limit,$pageCurr);
        $result = [
            'datas' => $datas,
            'prefix_url' => DOMAIN,
            'pagelist' => $pagelist,
            'model' => $this->getModel(),
            'cate' => $cate,
        ];
        return view('member.home.index', $result);
    }

    /**
     *  获取模板
     */
    public function getApply(Request $request)
    {
        if (!Session::has('user')) {
            echo "<script>alert('没有登录！');history.go(-1);</script>";exit;
        }
        $data = [
            'name'  =>  $request->name,
            'intro' =>  $request->intro,
            'tempid'=>  $request->tempid,
            'uid'   =>  $this->userid,
            'uname' =>  Session::get('user.username'),
        ];
        $rst = ApiProduct::add($data);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'u/product');
    }

//    public function store(Request $request){}

    public function show($id)
    {
        $rst = ApiProduct::show($id);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'data'=> $rst['data'],
            'model'=> $this->getModel(),
        ];
        return view('member.home.show', $result);
    }

    public function update(Request $request,$id)
    {
        $data = [
            'id'    =>  $id,
            'name'  =>  $request->name,
            'intro' =>  $request->intro,
            'uid'   =>  $this->userid,
        ];
        $rst = ApiProduct::modify($data);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'u/product/'.$id);
    }

    /**
     * 更新缩略图 thumb
     */
    public function setThumb(Request $request)
    {
        //图片验证
        if (!$request->url_file) {
            echo "<script>alert('没有上传图片！');history.go(-1);</script>";exit;
        }
        //去除老图片
        $oldImgArr = [];
        $rstProduct = ApiProduct::show($request->pro_id);
        if ($rstProduct['code']!=0) {
            echo "<script>alert('".$rstProduct['msg']."');history.go(-1);</script>";exit;
        }
        if ($rstProduct['data']['thumb']) {
            $thumbArr = explode('/',$rstProduct['data']['thumb']);
            if (mb_substr($thumbArr[0],0,4)=='http') {
                unset($thumbArr[0]); unset($thumbArr[1]); unset($thumbArr[2]);
                $thumb = implode('/',$thumbArr);
            } else {
                $thumb = ltrim($rstProduct['data']['thumb'],'/');
            }
            if (file_exists($thumb)) { $oldImgArr[] = $thumb; }
        }
        //链接类型验证
        $thumb = $this->uploadOnlyImg($request,'url_ori',$oldImgArr);
        if (!$thumb) {
            echo "<script>alert('缩略图有误！');history.go(-1);</script>";exit;
        }
        $data = [
            'id'    =>  $request->pro_id,
            'thumb' =>  $thumb,
            'uid'   =>  $this->userid,
        ];
        $apiTemp = ApiProduct::setThumb($data);
        if ($apiTemp['code']!=0) {
            echo "<script>alert('".$apiTemp['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'u/product/'.$request->pro_id);
    }

    /**
     * 更新视频链接 linkType、link
     */
    public function setLink(Request $request)
    {
        //判断视频链接
        $linkType = $request->linkType;
        $link = $request->link;
        $id = $request->pro_id;
        if (!$linkType || !$link ||!$id) {
            echo "<script>alert('数据有误！');history.go(-1);</script>";exit;
        }
        if ($linkType==1 && (mb_substr($link,0,4)!='http'||mb_substr($link,mb_strlen($link)-4,4)!='.swf')) {
            echo "<script>alert('Flash代码格式有误！');history.go(-1);</script>";exit;
        } elseif ($linkType==2 && mb_substr($link,0,6)!='<embed') {
            echo "<script>alert('html代码格式有误！');history.go(-1);</script>";exit;
        } elseif ($linkType==3 && mb_substr($link,0,7)!='<iframe') {
            echo "<script>alert('html代码格式有误！');history.go(-1);</script>";exit;
        }
        $data = [
            'id'    =>  $id,
            'linkType'  =>  $linkType,
            'link'  =>  $link,
            'uid'   =>  $this->userid,
        ];
        $apiProduct = ApiProduct::setLink($data);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'u/product/'.$id);
    }

    /**
     * 设置产品是否显示
     */
    public function setIsShow($id)
    {
        $apiProduct = ApiProduct::setIsShow($this->userid,$id);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'u/product/'.$id);
    }

    /**
     * 删除产品
     */
    public function forceDelete($id)
    {
        $uid = $this->userid;
        //判断产品缩略图
        $apiProduct = ApiProduct::getOneByUid($id,$uid);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        $apiTemp = ApiTempPro::show($apiProduct['data']['tempid']);
        if ($apiTemp['code']!=0) {
            echo "<script>alert('模板错误！');history.go(-1);</script>";exit;
        }
        $prothumb = $apiProduct['data']['thumb'];
        if ($prothumb!=$apiTemp['data']['thumb'] || $prothumb) {
            $thumbArr = explode('/',$apiProduct['data']['thumb']);
            if (mb_substr($thumbArr[0],0,4)=='http') {
                unset($thumbArr[0]); unset($thumbArr[1]); unset($thumbArr[2]);
                $thumb = implode('/',$thumbArr);
            } else {
                $thumb = ltrim($apiProduct['data']['thumb'],'/');
            }
            if (file_exists($thumb)) { unlink($thumb); }
        }
        //判断产品动画层缩略图
        $apiProLayers = ApiProLayer::index($uid,$id);
        if ($apiProLayers['code']!=0) {
            echo "<script>alert('".$apiProLayers['msg']."');history.go(-1);</script>";exit;
        }
        foreach ($apiProLayers['data'] as $proLayer) {
            $apiTempLayer = ApiTempPro::show($proLayer['tl_id']);
            if ($apiTempLayer['code']!=0) {
                echo "<script>alert('".$apiTempLayer['msg']."');history.go(-1);</script>";exit;
            }
            $apiTempCons = $apiTempLayer['data']['con'] ? unserialize($apiTempLayer['data']['con']) : [];
            $tconthumb = ($apiTempCons&&$apiTempCons['iscon']==2) ? $apiTempCons['img'] : '';
            $proCons = $proLayer['con'] ? unserialize($proLayer['con']) : [];
            $pconthumb = ($proCons&&$proCons['iscon']==2) ? $proCons['img'] : '';
            if ($pconthumb!=$tconthumb) {
                $thumbArr2 = explode('/',$pconthumb);
                if (mb_substr($thumbArr2[0],0,4)=='http') {
                    unset($thumbArr2[0]); unset($thumbArr2[1]); unset($thumbArr2[2]);
                    $thumb2 = implode('/',$thumbArr2);
                } else {
                    $thumb2 = ltrim($pconthumb,'/');
                }
                if (file_exists($thumb2)) { unlink($thumb2); }
            }
        }
        $rst = ApiProduct::deleteBy2Id($uid,$id);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'u/product');
    }

    /**
     * 产品动画整体预览
     */
    public function getPreview($id)
    {
        //判断有没关键帧
        $apiFrame = ApiProFrame::getFramesByProid($id);
        if ($apiFrame['code']!=0) {
            echo "<script>alert('没有关键帧预览！');history.go(-1);</script>";exit;
        }
        //判断有没缓存
        $apiProLayer = ApiProLayer::index($id);
        if ($apiProLayer['code']!=0) {
            echo "<script>alert('".$apiProLayer['msg']."');history.go(-1);</script>";exit;
        }
        $rediskey = 'online_user_'.$this->userid.'_layer_';
        foreach ($apiProLayer['data'] as $layer) {
            if (Redis::get($rediskey.$layer['id']))  { $hasRedisVal = 1; }
        }
        if (isset($hasRedisVal) && $hasRedisVal) {
            echo "<script>alert('请先保存或者取消已调整过的信息！');history.go(-1);</script>";exit;
        }
        $apiPro = ApiProduct::getPreview($id);
        if ($apiPro['code']!=0) {
            echo "<script>alert('".$apiPro['msg']."');history.go(-1);</script>";exit;
        }
        $proArr = $apiPro['data']['pro'];
        $proArr['layerNum'] = count($apiPro['data']['layer']);
        $apiLayer = ApiProLayer::index($id);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('没有预览！');history.go(-1);</script>";exit;
        }
        //判断用户更新的数据
        $result = [
            'product'   =>  $proArr,
            'layers'    =>  $apiLayer['data'],
        ];
        return view('member.layer.onepro', $result);
    }

    /**
     * 产品框架载入
     */
    public function getPrePro($id)
    {
        $apiProduct = ApiProduct::getPreview($id,2);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'product' => $apiProduct['data']['pro'],
            'layers' => $apiProduct['data']['layer'],
            'layerModel' => $this->getLayerModel(),
        ];
        return view('member.layer.prolayers', $result);
    }





    public function query($pageCurr,$cate)
    {
        $rst = ApiProduct::index($this->limit,$pageCurr,$this->userid,$cate,0);
        $datas = $rst['code']==0 ? $rst['data'] : [];
        return $datas;
    }

    /**
     * 获取 model
     */
    public function getModel()
    {
        $rst = ApiProduct::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
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