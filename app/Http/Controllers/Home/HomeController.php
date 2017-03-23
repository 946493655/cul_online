<?php
namespace App\Http\Controllers\Home;

use App\Api\ApiOnline\ApiTempPro;

class HomeController extends BaseController
{
    /**
     * 在线创作窗口主页
     */

    public function __construct()
    {
        parent::__construct();
    }

    public function index($cate=0)
    {
        $pageCurr = isset($_GET['page']) ? $_GET['page'] : 1;
        $apiTemp = ApiTempPro::index($this->limit,$pageCurr,$cate,2);
        if ($apiTemp['code']!=0) {
            $datas = array(); $total = 0;
        } else {
            $datas = $apiTemp['data']; $total = $apiTemp['pagelist']['total'];
        }
        $pagelist = $this->getPageList($total,DOMAIN,$this->limit,$pageCurr);
        $result = [
            'datas' => $datas,
            'pagelist' => $pagelist,
            'prefix_url' => DOMAIN,
            'model' => $this->getModel(),
            'cate' => $cate,
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
     * 获取 model
     */
    public function getModel()
    {
        $rst = ApiTempPro::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
    }
}