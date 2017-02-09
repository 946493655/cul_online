<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiTempFrame;
use App\Api\ApiOnline\ApiTempLayer;
use App\Api\ApiOnline\ApiTempPro;
use Illuminate\Http\Request;
use Redis;
use Illuminate\Support\Facades\Request as AjaxRequest;
use Illuminate\Support\Facades\Input;

class TempProController extends BaseController
{
    /**
     * 创作后台控制器
     */

    public function __construct()
    {
        parent::__construct();
        $this->curr = 'temp';
    }

    public function index($cate=0)
    {
        $pageCurr = isset($_POST['pageCurr'])?$_POST['pageCurr']:1;
        $prefix_url = DOMAIN.'admin/temp';
        $datas = $this->query($pageCurr,$cate);
        $pagelist = $this->getPageList($datas,$prefix_url,$this->limit,$pageCurr);
        $result = [
            'datas' => $datas,
            'pagelist' => $pagelist,
            'prefix_url' => $prefix_url,
            'model' => $this->getModel(),
            'cate' => $cate,
            'curr' => $this->curr,
        ];
        return view('admin.temp.index', $result);
    }

    public function store(Request $request)
    {
        $data = $this->getData($request);
        $rst = ApiTempPro::add($data);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/temp');
    }

    public function show($id)
    {
        $rst = ApiTempPro::show($id);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'data' => $rst['data'],
            'model' => $this->getModel(),
            'curr' => $this->curr,
        ];
        return view('admin.temp.show', $result);
    }

    public function update(Request $request,$id)
    {
        $data = $this->getData($request);
        $data['id'] = $id;
        $rst = ApiTempPro::modify($data);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/temp/'.$id);
    }

    /**
     * 设置缩略图
     */
    public function setThumb(Request $request,$id)
    {
        //图片验证
        if (!$request->url_file) {
            echo "<script>alert('没有上传图片！');history.go(-1);</script>";exit;
        }
        //去除老图片
        $oldImgArr = [];
        $rstTemp = ApiTempPro::show($id);
        if ($rstTemp['code']!=0) {
            echo "<script>alert('".$rstTemp['msg']."');history.go(-1);</script>";exit;
        }
        if ($rstTemp['data']['thumb']) {
            $thumbArr = explode('/',$rstTemp['data']['thumb']);
            if (mb_substr($thumbArr[0],0,4)=='http') {
                unset($thumbArr[0]); unset($thumbArr[1]); unset($thumbArr[2]);
                $thumb = implode('/',$thumbArr);
            } else {
                $thumb = ltrim($rstTemp['data']['thumb'],'/');
            }
            if (file_exists($thumb)) { $oldImgArr[] = $thumb; }
        }
        //链接类型验证
        $thumb = $this->uploadOnlyImg($request,'url_ori',$oldImgArr);
        if (!$thumb) {
            echo "<script>alert('缩略图有误！');history.go(-1);</script>";exit;
        }
        $data = [
            'id'    =>  $id,
            'thumb' =>  $thumb,
        ];
        $apiTemp = ApiTempPro::setThumb($data);
        if ($apiTemp['code']!=0) {
            echo "<script>alert('".$apiTemp['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/temp/'.$id);
    }

    /**
     * 设置视频链接
     */
    public function setLink(Request $request,$id)
    {
        //判断视频链接
        $linkType = $request->linkType;
        $link = $request->link;
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
        ];
        $apiTemp = ApiTempPro::setLink($data);
        if ($apiTemp['code']!=0) {
            echo "<script>alert('".$apiTemp['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/temp/'.$id);
    }

    /**
     * 设置 isshow
     */
    public function setIsShow()
    {
        if (AjaxRequest::ajax()) {
            $id = Input::get('id');
            $isshow = Input::get('isshow');
            if (!$id || !$isshow) {
                echo json_encode(array('code'=>-2, 'msg'=>'参数错误！'));exit;
            }
            $rst = ApiTempPro::setShow($id,$isshow);
            if ($rst['code']!=0) {
                echo json_encode(array('code'=>-3, 'msg'=>$rst['msg']));exit;
            }
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-1, 'msg'=>'数据错误！'));exit;
    }

    /**
     * 设置模板总背景
     */
    public function setTempBg(Request $request,$tempid)
    {
        if (!in_array($request->isbg,[0,1,2])) {
            echo "<script>alert('数据错误！');history.go(-1);</script>";exit;
        }
        if ($request->isbg==1 && !$request->bgcolor) {
            echo "<script>alert('背景色选择错误！');history.go(-1);</script>";exit;
        } elseif ($request->isbg==2 && !isset($request->url_ori)) {
            echo "<script>alert('背景图片未上传！');history.go(-1);</script>";exit;
        }
        if ($request->isbg==2) {
            $imgStr = $this->uploadOnlyImg($request,'url_ori');
            if (!$imgStr) {
                echo "<script>alert('图片上传有误！');history.go(-1);</script>";exit;
            }
        }
        $data = [
            'id'    =>  $request->tempid,
            'isbg'  =>  $request->isbg,
            'bgcolor'   =>  $request->bgcolor,
            'bgimg'   =>  isset($imgStr) ? $imgStr : '',
        ];
        $apiTemp = ApiTempPro::setAttr($data);
        if ($apiTemp['code']!=0) {
            echo "<script>alert('".$apiTemp['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/t/'.$request->tempid.'/layer');
    }

    /**
     * 销毁记录
     */
    public function forceDelete($id)
    {
        //假如有，去掉原图片
        $rstTemp = ApiTempPro::show($id);
        if ($rstTemp['code']==0 && $rstTemp['data']['thumb']) {
            $thumbArr = explode('/',$rstTemp['data']['thumb']);
            if (mb_substr($thumbArr[0],0,4)=='http') {
                unset($thumbArr[0]); unset($thumbArr[1]); unset($thumbArr[2]);
                $thumb = implode('/',$thumbArr);
            } else {
                $thumb = ltrim($rstTemp['data']['thumb'],'/');
            }
            if (file_exists($thumb)) {
                unlink($thumb);
            }
        }
        $rst = ApiTempPro::delete($id);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/temp');
    }

    /**
     * 清空表
     */
    public function clearTable()
    {
        //假如有数据，则获取数据，销毁图片
        $rstTemp = ApiTempPro::all();
        if ($rstTemp['code']!=0) {
            echo "<script>alert('".$rstTemp['msg']."');history.go(-1);</script>";exit;
        }
        foreach ($rstTemp['data'] as $temp) {
            if ($temp['thumb']) {
                $thumbArr = explode('/',$temp['thumb']);
                if (mb_substr($thumbArr[0],0,4)=='http') {
                    unset($thumbArr[0]); unset($thumbArr[1]); unset($thumbArr[2]);
                    $thumb = implode('/',$thumbArr);
                } else {
                    $thumb = ltrim($temp['thumb'],'/');
                }
                if (file_exists($thumb)) {
                    unlink($thumb);
                }
            }
        }
        $rst = ApiTempPro::clear(\Session::get('admin.username'));
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/temp');
    }

    /**
     * 模板动画预览
     */
    public function getPreview($id)
    {
        $apiFrame = ApiTempFrame::getFramesByTempid($id);
        if ($apiFrame['code']!=0) {
            echo "<script>alert('没有预览！');history.go(-1);</script>";exit;
        }
        $apiTemp = ApiTempPro::getPreview($id);
        if ($apiTemp['code']!=0) {
            echo "<script>alert('".$apiTemp['msg']."');history.go(-1);</script>";exit;
        }
        $temp = $apiTemp['data']['temp'];
        $temp['layerNum'] = count($apiTemp['data']['layer']);
        $apiLayer = ApiTempLayer::index($id,0);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('没有预览！');history.go(-1);</script>";exit;
        }
        $result = [
            'temp' => $temp,
            'layers' => $apiLayer['data'],
        ];
        return view('admin.layer.onetemp', $result);
    }

    /**
     * 模板的框架载入
     */
    public function getPreTemp($id)
    {
        $apiTemp = ApiTempPro::getPreview($id,2);
        if ($apiTemp['code']!=0) {
            echo "<script>alert('".$apiTemp['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'temp' => $apiTemp['data']['temp'],
            'layers' => $apiTemp['data']['layer'],
            'layerModel' => $this->getLayerModel(),
        ];
        return view('admin.layer.templayers', $result);
    }






    public function getData(Request $request)
    {
        return array(
            'name'  =>  $request->name,
            'cate'  =>  $request->cate,
            'intro' =>  $request->intro,
        );
    }

    public function query($pageCurr,$cate)
    {
        $rst = ApiTempPro::index($this->limit,$pageCurr,$cate);
        $datas = $rst['code']==0 ? $rst['data'] : [];
        return $datas;
    }

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
        $rst = ApiTempLayer::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
    }
}