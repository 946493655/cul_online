<?php
namespace App\Http\Controllers\Home;

use App\Api\ApiOnline\ApiOrder;

class OrderController extends BaseController
{
    /**
     * 前台渲染列表
     */

    public function __construct()
    {
        parent::__construct();
    }

    public function index($cate=0)
    {
        $pageCurr = isset($_GET['page']) ? $_GET['page'] : 1;
        $prefix_url = DOMAIN.'o';
        $apiOrder = ApiOrder::index($this->limit,$pageCurr,0,2);
        if ($apiOrder['code']!=0) {
            $datas = array(); $total = 0;
        } else {
            $datas = $apiOrder['data']; $total = $apiOrder['pagelist']['total'];
        }
        $pagelist = $this->getPageList($total,$prefix_url,$this->limit,$pageCurr);
        $result = [
            'datas' => $datas,
            'pagelist' => $pagelist,
            'prefix_url' => $prefix_url,
            'model' => $this->getModel(),
            'cate' => $cate,
        ];
        return view('home.order.index', $result);
    }

    /**
     * 获取model
     */
    public function getModel()
    {
        $rst = ApiOrder::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
    }
}