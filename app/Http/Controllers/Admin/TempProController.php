<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiTempPro;
use Illuminate\Http\Request;

class TempProController extends BaseController
{
    /**
     * 创作后台控制器
     */

    public function __construct()
    {
        parent::__construct();
    }

    public function index($cate=0)
    {
        $pageCurr = isset($_POST['pageCurr'])?$_POST['pageCurr']:1;
        $prefix_url = DOMAIN.'admin/temp';
        $result = [
            'datas'=> $this->query($pageCurr,$prefix_url,$cate),
            'prefix_url'=> $prefix_url,
            'model'=> $this->getModel(),
            'cate'=> $cate,
        ];
        return view('admin.temp.index', $result);
    }

    public function store(Request $request)
    {
        dd($request->all());
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
        ];
        return view('admin.temp.show', $result);
    }

    public function update(Request $request,$id)
    {
        dd($request->all());
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
        $rstTemp = ApiTempPro::show($id);
        if ($rstTemp['code']==0) {
            $thumb = $rstTemp['data']['thumb'];
            $imgArr = explode('/',$thumb);
            $imgStr = $imgArr[3].'/'.$imgArr[4].'/'.$imgArr[5].'/'.$imgArr[6];
            unlink($imgStr);
        }
        //链接类型验证
        $linkArr = $this->uploadImg($request,'url_ori');
        if (!$linkArr) {
            echo "<script>alert('缩略图或视频链接有误！');history.go(-1);</script>";exit;
        }
        dd($request->all());
        $data = [
            'thumb' =>  $linkArr['thumb'],
            'linkType'  =>  $linkArr['type'],
            'link'  =>  $linkArr['link'],
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
        $rst = ApiTempPro::delete($id);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'admin/temp');
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