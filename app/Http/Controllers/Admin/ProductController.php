<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiProFrame;
use App\Api\ApiOnline\ApiProLayer;
use App\Api\ApiOnline\ApiTempPro;
use App\Api\ApiUser\ApiUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as AjaxRequest;
use Illuminate\Support\Facades\Input;

class ProductController extends BaseController
{
    /**
     * 后台用户产品
     */

    public function index($cate=0)
    {
        $pageCurr = isset($_GET['pageCurr'])?$_GET['pageCurr']:1;
        $prefix_url = DOMAIN.'admin/product';
        $apiProduct = ApiProduct::index($this->limit,$pageCurr,0,0);
        if ($apiProduct['code']!=0) {
            $datas = array(); $total = 0;
        } else {
            $datas = $apiProduct['data']; $total = $apiProduct['pagelist']['total'];
        }
        $pagelist = $this->getPageList($total,$prefix_url,$this->limit,$pageCurr);
        $result = [
            'datas' => $datas,
            'pagelist' => $pagelist,
            'prefix_url' => $prefix_url,
            'model' => $this->getModel(),
            'temps' => $this->getTempAll(),
            'cate' => $cate,
            'curr' => $this->curr,
        ];
        return view('admin.product.index', $result);
    }

    public function store(Request $request)
    {
        $apiUser = ApiUsers::getOneUserByUname($request->uname);
        if ($apiUser['code']!=0) {
            echo "<script>alert('".$apiUser['msg']."');history.go(-1);</script>";exit;
        }
        $data = [
            'name'  =>  $request->name,
            'intro' =>  $request->intro,
            'tempid'    =>  $request->tempid,
            'uid'   =>  $apiUser['data']['id'],
            'uname' =>  $apiUser['data']['username'],
        ];
        $apiProduct = ApiProduct::add($data);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/product');
    }

    public function show($id)
    {
        $apiProduct = ApiProduct::show($id);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'data' => $apiProduct['data'],
            'model' => $this->getModel(),
            'curr' => $this->curr,
        ];
        return view('admin.product.show', $result);
    }

    public function update(Request $request,$id)
    {
        $data = [
            'id'    =>  $request->id,
            'name'  =>  $request->name,
            'cate'  =>  $request->cate,
            'intro' =>  $request->intro,
            'uid'   =>  $request->uid,
        ];
        $apiProduct = ApiProduct::modify($data);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/product/'.$id);
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
        $apiProduct = ApiProduct::show($id);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        if ($apiProduct['data']['thumb']) {
            $thumbArr = explode('/',$apiProduct['data']['thumb']);
            if (mb_substr($thumbArr[0],0,4)=='http') {
                unset($thumbArr[0]); unset($thumbArr[1]); unset($thumbArr[2]);
                $thumb = implode('/',$thumbArr);
            } else {
                $thumb = ltrim($apiProduct['data']['thumb'],'/');
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
            'uid'   =>  $request->uid,
        ];
        $apiProduct2 = ApiProduct::setThumb($data);
        if ($apiProduct2['code']!=0) {
            echo "<script>alert('".$apiProduct2['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/product/'.$id);
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
            'uid'   =>  $request->uid,
        ];
        $apiProduct = ApiProduct::setLink($data);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/product/'.$id);
    }

    /**
     * 设置产品总背景
     */
    public function setProBg(Request $request,$id)
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
            'id'    =>  $request->pro_id,
            'isbg'  =>  $request->isbg,
            'bgcolor'   =>  $request->bgcolor,
            'bgimg'   =>  isset($imgStr) ? $imgStr : '',
        ];
        $apiTemp = ApiProduct::setAttr($data);
        if ($apiTemp['code']!=0) {
            echo "<script>alert('".$apiTemp['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/pro/'.$request->pro_id.'/layer');
    }

    /**
     * 设置是否显示
     */
    public function setShow(Request $request,$id)
    {
        if (AjaxRequest::ajax()) {
            $apiProduct = ApiProduct::setShow($request->id,$request->isshow);
            if ($apiProduct['code']!=0) {
                echo json_encode(array('code'=>-2, 'msg'=>'参数有误！'));exit;
            }
            echo json_encode(array('code'=>0, 'msg'=>'操作成功！'));exit;
        }
        echo json_encode(array('code'=>-1, 'msg'=>'参数错误！'));exit;
    }

    /**
     * 预览整体
     */
    public function getPreview($id)
    {
        $apiFrame = ApiProFrame::getFramesByProid($id);
        if ($apiFrame['code']!=0) {
            echo "<script>alert('没有预览！');history.go(-1);</script>";exit;
        }
        $apiProduct = ApiProduct::getPreview($id,2);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        $product = $apiProduct['data']['pro'];
        $product['layerNum'] = count($apiProduct['data']['layer']);
        $apiLayer = ApiProLayer::index($id,2);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('没有预览！');history.go(-1);</script>";exit;
        }
        $result = [
            'product' => $product,
            'layers' => $apiLayer['data'],
        ];
        return view('admin.product.layer.onepro', $result);
    }

    /**
     * 模板的框架载入
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
        return view('admin.product.layer.prolayers', $result);
    }







    /**
     * 获取 model
     */
    public function getModel()
    {
        $apiModel = ApiProduct::getModel();
        return $apiModel['code']==0 ? $apiModel['model'] : [];
    }

    /**
     * 获取 model
     */
    public function getLayerModel()
    {
        $apiLayerModel = ApiProLayer::getModel();
        return $apiLayerModel['code']==0 ? $apiLayerModel['model'] : [];
    }

    /**
     * 获取所有模板
     */
    public function getTempAll()
    {
        $apiTemp = ApiTempPro::all();
        return $apiTemp['code']==0 ? $apiTemp['data'] : [];
    }
}