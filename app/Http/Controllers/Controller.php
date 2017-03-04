<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    protected $limit = 20;          //每页显示记录数
    protected $model;
    protected $redisTime = 60 * 60 * 2;       //session在redis中缓存时长，单位秒，默认2小时
    protected $uploadSizeLimit = 1 * 1024 * 1023;       //限制上传图片尺寸1M
    protected $suffix_img = [       //图片允许后缀
        "png", "jpg", "gif", "bmp", "jpeg", "jpe",
    ];

    public function __construct()
    {
        define("DOMAIN",getenv('DOMAIN'));
        define("PUB",getenv('PUB'));
    }

    /**
     * 接口分页处理
     */
    public function getPageList($datas,$prefix_url,$limit,$pageCurr=1)
    {
        $currentPage = $pageCurr;                               //当前页
        $lastPage = ($pageCurr - 1) ? ($pageCurr - 1) : 1;      //上一页
        $total = count($datas);                                 //总记录数
        //上一页路由
        if ($pageCurr<=1) {
            $previousPageUrl = $prefix_url;
        } else {
            $previousPageUrl = $prefix_url.'?page='.($pageCurr-1);
        }
        //下一页路由
        if (count($datas) <= $limit) {
            $nextPageUrl = $prefix_url;
        } elseif ($pageCurr * $limit >= count($datas)) {
            $nextPageUrl = $prefix_url.'?page='.$pageCurr;
        } else {
            $nextPageUrl = $prefix_url.'?page='.($pageCurr+1);
        }
        return array(
            'currentPage'   =>  $currentPage,
            'lastPage'      =>  $lastPage,
            'total'         =>  $total,
            'limit'         =>  $limit,
            'previousPageUrl'   =>  $previousPageUrl,
            'nextPageUrl'   =>  $nextPageUrl,
        );
    }

    /**
     * 定义一个方法，获取用户端ip
     */
    public function getIp()
    {
        if (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "";
        }
        return $ip;
    }

    /**
     * 由ip获得所在城市
     */
    public function getCityByIp($ip='')
    {
        $address = '';
        if ($ip && substr($ip,0,7)!='192.168') {
            $key = 'Tj1ciyqmG0quiNgpr0nmAimUCCMB5qMk';      //自己申请的百度地图api的key
            $curl = new \Curl\Curl();
            $apiUrl = 'http://api.map.baidu.com/location/ip';
            $curl->post($apiUrl, array(
                'ak'=> $key,
                'ip'=> $ip,
            ));
            $response = $curl->response;
            $response = json_decode(json_encode($response),true);
            if ($response['status']==0) {
                $address = $response['content']['address'];
            }
        } elseif ($ip && substr($ip,0,7)=='192.168') {
            $address = '浙江省 杭州市 滨江区';
        } elseif (!$ip) {
            $address = '未知';
        }
        return $address;
    }

    /**
     * 上传方法，并处理文件
     */
    public function upload($file)
    {
        if($file->isValid()){
            $allowed_extensions = $this->suffix_img;
            if ($file->getClientOriginalExtension() &&
                !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
                echo "<script>alert('你的图片格式不对！');history.go(-1);</script>";exit;
            }
            $extension       = $file->getClientOriginalExtension() ?: 'png';
            $folderName      = '/uploads/images/'.date('Y-m-d', time()).'/';
            $destinationPath = public_path().$folderName;
            $safeName        = uniqid().'.'.$extension;
            $file->move($destinationPath, $safeName);
            $filePath = rtrim(DOMAIN,'/').$folderName.$safeName;
            return $filePath;
        } else {
            return "没有图片！";
        }
    }

    /**
     * 只上传图片，返回图片地址
     */
    public function uploadOnlyImg($request,$imgName='url_ori',$oldImgArr=[])
    {
        if($request->hasFile($imgName)){        //判断图片存在
            //去除老图片
            if ($oldImgArr) {
                foreach ($oldImgArr as $oldImg) { unlink($oldImg); }
            }
            foreach ($_FILES as $img) {
                if ($img['size'] > $this->uploadSizeLimit) {
                    echo "<script>alert('上传的图片不能大于1M，请重新选择！');history.go(-1);</script>";exit;
                }
            }
            $file = $request->file($imgName);           //获取图片
            return $this->upload($file);
        } else {
            return '';
        }
    }
}
