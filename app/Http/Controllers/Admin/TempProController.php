<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiTempFrame;
use App\Api\ApiOnline\ApiTempPro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
        $result = [
            'datas' => $this->query($pageCurr,$prefix_url,$cate),
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
     * 设置2链接：thumb、linkType、link
     */
    public function set2Link(Request $request,$id)
    {
        //图片验证
        if (!$request->url_file) {
            echo "<script>alert('没有上传图片！');history.go(-1);</script>";exit;
        }
        //去除老图片
        $oldImg = '';
        $rstTemp = ApiTempPro::show($id);
        if ($rstTemp['code']==0 && $rstTemp['data']['thumb']) {
            $thumbArr = explode('/',$rstTemp['data']['thumb']);
            if (mb_substr($thumbArr[0],0,4)=='http') {
                unset($thumbArr[0]); unset($thumbArr[1]); unset($thumbArr[2]);
                $thumb = implode('/',$thumbArr);
            } else {
                $thumb = ltrim($rstTemp['data']['thumb'],'/');
            }
            if (file_exists($thumb)) { $oldImg = $thumb; }
        }
        //链接类型验证
        $linkArr = $this->uploadImg($request,'url_ori',$oldImg);
        if (!$linkArr) {
            echo "<script>alert('缩略图或视频链接有误！');history.go(-1);</script>";exit;
        }
        $data = [
            'thumb' =>  $linkArr['thumb'],
            'linkType'  =>  $linkArr['linkType'],
            'link'  =>  $linkArr['link'],
            'id'    =>  $id,
        ];
        $rst = ApiTempPro::modify2Link($data);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/temp/'.$id);
    }

    /**
     * 设置 isshow
     */
    public function setIsShow($id,$isshow)
    {
        $rst = ApiTempPro::isShow($id,$isshow);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/temp/'.$id);
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
        $apiKey = ApiTempFrame::getFramesByTempid($id);
        if ($apiKey['code']!=0) {
            echo "<script>alert('没有预览！');history.go(-1);</script>";exit;
        }
        $rst = ApiTempPro::getPreview($id);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'layers' => $rst['data'],
            'tempid' => $id,
        ];
        return view('admin.layer.onetemp', $result);
    }

    /**
     * 模板的框架载入
     */
    public function getPreTemp($id)
    {
    }






    public function getData(Request $request)
    {
        return array(
            'name'  =>  $request->name,
            'cate'  =>  $request->cate,
            'intro' =>  $request->intro,
        );
    }

    public function query($pageCurr,$prefix_url,$cate)
    {
        $rst = ApiTempPro::index($this->limit,$pageCurr,$cate);
        $datas = $rst['code']==0 ? $rst['data'] : [];
        $datas['pagelist'] = $this->getPageList($datas,$prefix_url,$this->limit,$pageCurr);
        return $datas;
    }

    public function getModel()
    {
        $rst = ApiProduct::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
    }
}