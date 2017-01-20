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

class AdminAuth
{
    public function handle($request, Closure $next)
    {
        //判断系统后台有无此登录的用户
        if(!Session::get('admin') && !Redis::get('cul_admin_session')){
            return redirect('/admin/login');
//            echo "<script>alert('还没有登录！');window.location.href='/admin/login';</script>";exit;
        }
        return $next($request);
    }
}