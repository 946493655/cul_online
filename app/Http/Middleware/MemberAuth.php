<?php
/**
 * Created by PhpStorm.
 * User: liubin
 * Date: 15/4/20
 * Time: 22:46
 */

namespace App\Http\Middleware;

use Session;
use Closure;

class MemberAuth
{
    public function handle($request, Closure $next)
    {
        //判断会员后台有无此登录的用户
        if(!Session::get('user')){
            echo "<script>alert('还没有登录！');window.location.href='/login';</script>";exit;
        }
        return $next($request);
    }
}