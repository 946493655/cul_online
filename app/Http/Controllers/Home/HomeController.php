<?php
namespace App\Http\Controllers\Home;

use App\Api\ApiOnline\ApiTempPro;

class HomeController extends BaseController
{
    /**
     * 在线创作窗口主页
     */

    protected $limit = 12;

    public function __construct()
    {
        parent::__construct();
    }

    public function index($cate=0)
    {
        $pageCurr = isset($_POST['pageCurr'])?$_POST['pageCurr']:1;
        $prefix_url = DOMAIN;
        $result = [
            'datas'=> $this->query($pageCurr,$prefix_url,$cate),
            'prefix_url'=> $prefix_url,
            'model'=> $this->getModel(),
            'cate'=> $cate,
        ];
        return view('home.home.index', $result);
    }

    public function show($id)
    {
        $rst = ApiTempPro::getOneByShow($id,2);
        if ($rst['code']!=0) {
            echo "<script>alert('".$rst['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'data'=> $rst['data'],
        ];
        return view('home.home.show', $result);
    }




    /**
     * 以下是要展示的数据
     */

    public function query($pageCurr,$prefix_url,$cate=0)
    {
        $rst = ApiTempPro::index($this->limit,$pageCurr,$cate,2);
        $datas = $rst['code']==0?$rst['data']:[];
        $datas['pagelist'] = $this->getPageList($datas,$prefix_url,$this->limit,$pageCurr);
        return $datas;
    }

    /**
     * 获取 model
     */
    public function getModel()
    {
        $rst = ApiTempPro::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
    }
}