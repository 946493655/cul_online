<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Session;
use Redis;

class BaseController extends Controller
{
    /**
     * 系统后台基础控制器
     */

    protected $curr;

    public function __construct()
    {
        parent::__construct();
        $this->setSessionInRedis($this->redisTime);     //同步缓存中session
        if (!Session::has('admin')) { return redirect(DOMAIN.'admin/login'); }
    }

    /**
     * 判断session、缓存
     */
    public function setSessionInRedis($redisTime)
    {
        //假如session中有，缓存中没有，则同步为有
        if (Session::get('admin') && !Redis::get('cul_admin_session')) {
            $adminInfo = Session::get('admin');
            $adminInfo['cookie'] = $_COOKIE;
            Redis::setex('cul_admin_session',$redisTime,serialize($adminInfo));
        }
        //假如session中没有，缓存中有，则同步为有
        if (!Session::get('admin') && Redis::get('cul_admin_session')) {
            $cul_admin_session = unserialize(Redis::get('cul_admin_session'));
            $cul_admin_session['cookie'] = $_COOKIE;
            if ($cul_admin_session['cookie']['laravel_session']!=$_COOKIE['laravel_session']) {
                echo 'no';exit;
            }
            Session::put('admin',$cul_admin_session);
        }
        //更新session中的cookie值
        if (Session::get('admin')) {
            $cul_admin_session = Session::get('admin');
            $cul_admin_session['cookie'] = $_COOKIE;
            Redis::setex('cul_admin_session',$redisTime,serialize($cul_admin_session));
            Session::put('admin',$cul_admin_session);
        }
    }
}