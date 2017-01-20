<?php
/**
 * Created by PhpStorm.
 * User: liubin
 * Date: 15/4/20
 * Time: 22:46
 */

namespace App\Http\Middleware;

use Closure;
use Session;
use Redis;

class MemberAuth
{
    public function handle($request, Closure $next)
    {
        //判断会员后台有无此登录的用户
        if(!Session::get('user')){
            echo "<script>alert('还没有登录！');window.location.href='/login';</script>";exit;
        }
        //更新session
        $this->setSessionInRedis();     //同步缓存中session
        return $next($request);
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