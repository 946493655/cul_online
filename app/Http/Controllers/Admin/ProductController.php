<?php
namespace App\Http\Controllers\Admin;

use App\Api\ApiOnline\ApiProduct;

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
            'cate' => $cate,
            'curr' => $this->curr,
        ];
        return view('admin.product.index', $result);
    }

    /**
     * 获取 model
     */
    public function getModel()
    {
        $apiModel = ApiProduct::getModel();
        return $apiModel['code']==0 ? $apiModel['model'] : [];
    }
}