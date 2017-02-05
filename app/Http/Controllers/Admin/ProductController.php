<?php
namespace App\Http\Controllers\Admin;

class ProductController extends BaseController
{
    /**
     * 用户产品
     */

    public function index()
    {
        return view('admin.product.index');
    }
}