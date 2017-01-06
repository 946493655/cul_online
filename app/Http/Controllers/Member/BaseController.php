<?php
namespace App\Http\Controllers\Member;

use App\Http\Controllers\BaseController as Controller;
use Session;

class BaseController extends Controller
{
    /**
     * 用户房间基础控制器
     */

    public function __construct()
    {
        parent::__construct();
        if (!Session::has('user')) {
            echo "<script>alert('没有登录！');window.location.href='/login';</script>";exit;
        }
    }
}