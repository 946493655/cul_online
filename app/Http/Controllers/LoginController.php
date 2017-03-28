<?php
namespace App\Http\Controllers;

use App\Api\ApiUser\ApiUsers;
use Illuminate\Support\Facades\Input;
use Hash;
use Session;
use Redis;

class LoginController extends BaseController
{
    /**
     * 会员注册页面
     * 支持手机、邮箱、用户名
     */

    public function index()
    {
        return view('login');
    }

    public function dologin()
    {
        //用户、密码验证
        $rstUser = ApiUsers::getOneUserByUname(Input::get('username'));
        if ($rstUser['code']!=0) {
            echo "<script>alert('".$rstUser['msg']."');history.go(-1);</script>";exit;
        } elseif (!(Hash::check(Input::get('password'),$rstUser['data']['password']))) {
            //验证密码正确否
            echo "<script>alert('密码错误！');history.go(-1);</script>";exit;
        }

        //接口验证数据，写入用户表，或者返回错误信息
        $ip = $this->getIp();
        $data = [
            'username'=> Input::get('username'),
            'password'=> Hash::make(Input::get('password')),
            'pwd'=> Input::get('password'),
            'ip'=> $ip,
            //以下用户日志用
            'ipaddress'=> $this->getCityByIp($ip),
            'genre'=> 3,      //3代表用户online,4代表管理员online
            'action'=> $_SERVER['REQUEST_URI'],
        ];
        $rstLogin = ApiUsers::doLogin($data);
        if ($rstLogin['code'] != 0) {
            echo "<script>alert('".$rstLogin['msg']."');history.go(-1);</script>";exit;
        }

        //个人资料
        if (in_array($rstLogin['data']['isuser'],[1,2,4,50])) {
            $personInfo = ApiUsers::getPersonInfo($rstLogin['data']['id']);
            if ($personInfo['code'] != 0) {
                $person = array();
            } else {
                $person['per_id'] = $personInfo['data']['id'];
                $person['realname'] = $personInfo['data']['realname'];
                $person['sex'] = $personInfo['data']['sex'];
                $person['idcard'] = $personInfo['data']['idcard'];
                $person['idfront'] = $personInfo['data']['idfront'];
            }
        }
        //企业资料
        if (in_array($rstLogin['data']['isuser'],[3,5,6,7,50])) {
            $companyInfo = ApiUsers::getOneCompany($rstLogin['data']['id']);
            if ($companyInfo['code'] != 0) {
                $company = array();
            } else {
                $company['cid'] = $companyInfo['data']['id'];
                $company['name'] = $companyInfo['data']['name'];
                $company['area'] = $companyInfo['data']['area'];
                $company['address'] = $companyInfo['data']['address'];
                $company['yyzzid'] = $companyInfo['data']['yyzzid'];
                $company['logo'] = $companyInfo['data']['logo'];
                $company['skin'] = $companyInfo['data']['skin'];
                $company['layout'] = $companyInfo['data']['layout'];
            }
        }

        $serial = date('YmdHis',time()).rand(0,10000);
        $userInfo = [
            'uid'=> $rstLogin['data']['id'],
            'username'=> Input::get('username'),
            'email'=> $rstLogin['data']['email'],
            'userType'=> $rstLogin['data']['isuser'],
            'serial'=> $serial,
            'area'=> $rstLogin['data']['area'],
            'address'=> $rstLogin['data']['address'],
            'cid'=> isset($companyInfo['data'])?$companyInfo['data']['id']:0,
            'loginTime'=> time(),
            'person'=> isset($person) ? $person : [],
            'company'=> isset($company) ? $company : [],
        ];
        $userInfo['cookie'] = $_COOKIE;
        Session::put('user',$userInfo);

        //将session放入redis
        Redis::setex('cul_session', $this->redisTime, serialize($userInfo));

        return redirect(DOMAIN.'u/product');
    }

    public function logout()
    {
        //更新用户日志表
        $rstLog = ApiUsers::logout(Session::get('user.serial'));
        if ($rstLog['code']!=0) {
            echo "<script>alert('".$rstLog['msg']."');history.go(-1);</script>";exit;
        }
        //去除session
        Session::forget('user');
        Redis::del('cul_session');
        return redirect(DOMAIN.'login');
    }
}