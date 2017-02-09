<?php
namespace App\Http\Controllers\Member;

use App\Api\ApiOnline\ApiOrder;
use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiProLayer;

class OrderController extends BaseController
{
    /**
     * 渲染订单
     */

    public function index(){}

    public function create($pro_id)
    {
        $apiProduct = ApiProduct::show($pro_id);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        //判断用户更新过的数据：动画设置、动画内容、动画属性、关键帧
//        $apiLayer = ApiProLayer::index($pro_id);
        $result = [
            'product'   =>  $apiProduct['data'],
            'model'     =>  $this->getModel(),
        ];
        return view('member.order.create', $result);
    }

    /**
     * 获取 model
     */
    public function getModel()
    {
        $rst = ApiOrder::getModel();
        return $rst['code']==0 ? $rst['model'] : [];
    }
}