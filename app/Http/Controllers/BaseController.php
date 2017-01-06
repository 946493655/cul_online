<?php
namespace App\Http\Controllers;

use Session;
use Redis;

class BaseController extends Controller
{
    /**
     * 前台、用户基础控制器
     */

    protected $userid;

    public function __construct()
    {
        parent::__construct();
        $this->setSessionInRedis();     //同步缓存中session
    }

    /**
     * 判断session、缓存
     */
    public function setSessionInRedis()
    {
        //假如session中有，缓存中没有，则同步为有
        if (Session::get('user') && !Redis::get('cul_session')) {
            $userInfo = Session::get('user');
            $userInfo['cookie'] = $_COOKIE;
            Redis::setex('cul_session',$this->redisTime,serialize($userInfo));
        }
        //假如session中没有，缓存中有，则同步为有
        if (!Session::get('user') && Redis::get('cul_session')) {
            $cul_session = unserialize(Redis::get('cul_session'));
            $cul_session['cookie'] = $_COOKIE;
            if ($cul_session['cookie']['laravel_session']!=$_COOKIE['laravel_session']) {
                echo 'no';exit;
            }
            Session::put('user',$cul_session);
        }
        //更新session中的cookie值
        if (Session::get('user')) {
            $cul_session = Session::get('user');
            $cul_session['cookie'] = $_COOKIE;
            Redis::setex('cul_session',$this->redisTime,serialize($cul_session));
            Session::put('user',$cul_session);
        }
    }
}