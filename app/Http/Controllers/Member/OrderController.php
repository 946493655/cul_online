<?php
namespace App\Http\Controllers\Member;

use App\Api\ApiOnline\ApiOrder;
use App\Api\ApiOnline\ApiProduct;
use App\Api\ApiOnline\ApiProFrame;
use App\Api\ApiOnline\ApiProLayer;
use App\Api\ApiOnline\ApiTempFrame;
use App\Api\ApiOnline\ApiTempLayer;
use App\Api\ApiUser\ApiWallet;
use Illuminate\Http\Request;
use Session;

class OrderController extends BaseController
{
    /**
     * 渲染订单
     */

    protected $limit = 15;
    //更新的记录信息
    protected $attrArr = ['bigbg','bg','width','height','border1','border2','border3','color','fontsize'];
    protected $unitPrice = 5;   //更新记录的单价

    public function index($cate=0)
    {
        $pageCurr = isset($_GET['page']) ? $_GET['page'] : 1;
        $apiOrder = ApiOrder::index($this->limit,$pageCurr,$this->userid,$cate,0,2);
        $datas = $apiOrder['code']==0 ? $apiOrder['data'] : [];
        $prefix_url = DOMAIN.'o';
        $pagelist = $this->getPageList($datas,$prefix_url,$this->limit,$pageCurr);
        $result = [
            'datas' => $datas,
            'pagelist' => $pagelist,
            'prefix_url' => $prefix_url,
            'model' =>  $this->getModel(),
            'cate'  =>  $cate,
        ];
        return view('member.order.index', $result);
    }

    public function create($pro_id)
    {
        $apiProduct = ApiProduct::show($pro_id);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        $product = $apiProduct['data'];
        $apiProLayer = ApiProLayer::index($pro_id);
        if ($apiProLayer['code']!=0) {
            echo "<script>alert('".$apiProLayer['msg']."');history.go(-1);</script>";exit;
        }
        $product['layerCount'] = count($apiProLayer['data']);
        //判断用户更新过的数据：动画设置、动画内容、动画属性、关键帧
        $userModifyArr = $this->getUserModify($pro_id);
        //用户钱包
        $apiWallet = ApiWallet::getWalletByUid($this->userid);
        if ($apiWallet['code']!=0) {
            echo "<script>alert('".$apiWallet['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'product'   =>  $product,
            'userModifyArr' =>  $userModifyArr,
            'wallet'    =>  $apiWallet['data'],
            'model'     =>  $this->getModel(),
            'unitPrice' =>  $this->unitPrice,
        ];
        return view('member.order.create', $result);
    }

    /**
     * 判断用户更新的记录：动画设置、动画内容、动画属性、关键帧
     */
    public function getUserModify($pro_id)
    {
        $apiLayer = ApiProLayer::index($pro_id);
        if ($apiLayer['code']!=0) {
            echo "<script>alert('".$apiLayer['msg']."');history.go(-1);</script>";exit;
        }
        $apiFrame = ApiProFrame::getFramesByProid($pro_id);
        if ($apiFrame['code']!=0) {
            echo "<script>alert('".$apiFrame['msg']."');history.go(-1);</script>";exit;
        }
        static $layerNum = 0;
        static $conNum = 0;
        static $attrNum = 0;
        static $frameNum = 0;
        foreach ($apiLayer['data'] as $layer) {
            $apiTLayer = ApiTempLayer::show($layer['tl_id']);
            if ($apiTLayer['code']==0) {
                $attrArr = $layer['attr'] ? unserialize($layer['attr']) : [];
                $tLayer = $apiTLayer['data'];
                $tAttrArr = $tLayer['attr'] ? unserialize($tLayer['attr']) : [];
                if ($layer['delay']!=$tLayer['delay']) { $layerNum ++; }
                if ($layer['timelong']!=$tLayer['timelong']) { $layerNum ++; }
                if ($layer['con']!=$tLayer['con']) { $conNum ++; }
                foreach ($this->attrArr as $attr) {
                    if ($attrArr[$attr]!=$tAttrArr[$attr]) { $attrNum ++; }
                }
            }
        }
        foreach ($apiFrame['data'] as $frame) {
            $apiTFrame = ApiTempFrame::getFrameByTFid($frame['tf_id']);
            $tframe = $apiTFrame['code']==0 ? $apiTFrame['data'] : [];
            if ($tframe['per']!=$frame['per'] || $tframe['val']!=$frame['val']) { $frameNum ++; }
        }
        return array(
            'layerNum'  =>  $layerNum,
            'conNum'    =>  $conNum,
            'attrNum'   =>  $attrNum,
            'frameNum'  =>  $frameNum,
            'countNum'  =>  $layerNum+$conNum+$attrNum+$frameNum,
        );
    }

    public function store(Request $request)
    {
        $apiProduct = ApiProduct::getOneByUid($request->pro_id,$this->userid);
        if ($apiProduct['code']!=0) {
            echo "<script>alert('".$apiProduct['msg']."');history.go(-1);</script>";exit;
        }
        $data = [
            'pro_id'    =>  $request->pro_id,
            'cate'      =>  $apiProduct['data']['cate'],
            'uid'       =>  $this->userid,
            'uname'     =>  Session::get('user.username'),
            'format'    =>  $request->kformat,
            'money'    =>  $request->money,
            'weal'      =>  $request->weal,
            'money1'    =>  $request->money - $request->weal,
        ];
        $apiOrder = ApiOrder::add($data);
        if ($apiOrder['code']!=0) {
            echo "<script>alert('".$apiOrder['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'o');
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