<?php
namespace App\Http\Controllers\Member;

use App\Api\ApiOnline\ApiProduct;

class HomeController extends BaseController
{
    /**
     * 用户创作管理控制器
     */

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

    /**
     * 获取 model
     */
    public function getModel()
    {
        $rst = ApiProduct::getModel();
    }
}