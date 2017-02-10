<?php
namespace App\Http\Controllers\Member;

use App\Api\ApiUser\ApiUsers;
use App\Api\ApiUser\ApiWallet;

class AccountController extends BaseController
{
    /**
     * 用户账户中心
     */

    protected $wealBySign = 30;     //30签到兑换1福利
    protected $wealByGold = 10;     //10金币兑换1福利
    protected $wealByTip = 1;       //1红包兑换1福利

    public function index()
    {
        $apiUser = ApiUsers::getOneUser($this->userid);
        if ($apiUser['code']!=0) {
            echo "<script>alert('".$apiUser['msg']."');history.go(-1);</script>";exit;
        }
        $apiWallet = ApiWallet::getWalletByUid($this->userid);
        if ($apiWallet['code']!=0) {
            echo "<script>alert('".$apiWallet['msg']."');history.go(-1);</script>";exit;
        }
        $result = [
            'userInfo'  =>  $apiUser['data'],
            'wallet'    =>  $apiWallet['data'],
            'wealBySign'    =>  $this->wealBySign,
            'wealByGold'    =>  $this->wealByGold,
            'wealByTip'    =>  $this->wealByTip,
        ];
        return view('member.account.index', $result);
    }

    /**
     * 签到兑换福利 30
     */
    public function getWealBySign($weal)
    {
        $apiWallet = ApiWallet::getWalletByUid($this->userid);
        if ($apiWallet['code']!=0) {
            echo "<script>alert('".$apiWallet['msg']."');history.go(-1);</script>";exit;
        }
        if ($weal*$this->wealBySign > $apiWallet['data']['sign']) {
            echo "<script>alert('签到数量不足！');history.go(-1);</script>";exit;
        }
        $rstWallet = ApiWallet::setConvert($this->userid,1,$weal);
        if ($rstWallet['code']!=0) {
            echo "<script>alert('".$rstWallet['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'myinfo');
    }

    /**
     * 金币兑换福利 10
     */
    public function getWealByGold($weal)
    {
        $apiWallet = ApiWallet::getWalletByUid($this->userid);
        if ($apiWallet['code']!=0) {
            echo "<script>alert('".$apiWallet['msg']."');history.go(-1);</script>";exit;
        }
        if ($weal*$this->wealByGold > $apiWallet['data']['gold']) {
            echo "<script>alert('金币数量不足！');history.go(-1);</script>";exit;
        }
        $rstWallet = ApiWallet::setConvert($this->userid,2,$weal);
        if ($rstWallet['code']!=0) {
            echo "<script>alert('".$rstWallet['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'myinfo');
    }

    /**
     * 红包兑换福利 1
     */
    public function getWealByTip($weal)
    {
        $apiWallet = ApiWallet::getWalletByUid($this->userid);
        if ($apiWallet['code']!=0) {
            echo "<script>alert('".$apiWallet['msg']."');history.go(-1);</script>";exit;
        }
        if ($weal*$this->wealByTip > $apiWallet['data']['tip']) {
            echo "<script>alert('红包数量不足！');history.go(-1);</script>";exit;
        }
        $rstWallet = ApiWallet::setConvert($this->userid,3,$weal);
        if ($rstWallet['code']!=0) {
            echo "<script>alert('".$rstWallet['msg']."');history.go(-1);</script>";exit;
        }
        return redirect(DOMAIN.'myinfo');
    }
}