<?php
namespace App\Api\ApiOnline;

use Curl\Curl;

class ApiTempPro
{
    /**
     * 在线创作模板接口
     */

    public static function index($limit=0,$pageCurr=1,$cate=0,$isshow=0)
    {
        $redisKey = 'tempProList';
        //判断缓存有没有该数据
        if ($redisResult = ApiBase::getRedis($redisKey)) {
            return array('code' => 0, 'data' => unserialize($redisResult));
        }
        //没有，接口读取
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'limit' =>  $limit,
            'page'  =>  $pageCurr,
            'cate'  =>  $cate,
            'isshow'  =>  $isshow,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array(
            'code' => 0,
            'model' => ApiBase::objToArr($response->model),
            'data' => ApiBase::objToArr($response->data),
        );
    }

    /**
     * 获取所有数据
     */
    public static function all()
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/all';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array(
            'code' => 0,
            'model' => ApiBase::objToArr($response->model),
            'data' => ApiBase::objToArr($response->data),
        );
    }

    /**
     * 根据 id、uid 获取记录
     */
    public static function getOneByShow($id,$isshow)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/getonebyshow';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'id'    =>  $id,
            'isshow'   =>  $isshow,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array(
            'code' => 0,
            'model' => ApiBase::objToArr($response->model),
            'data' => ApiBase::objToArr($response->data),
        );
    }

    /**
     * 根据 id 获取记录
     */
    public static function show($id)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/show';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'id'    =>  $id,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array(
            'code' => 0,
            'model' => ApiBase::objToArr($response->model),
            'data' => ApiBase::objToArr($response->data),
        );
    }

    /**
     * 添加模板
     */
    public static function add($data)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/add';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, $data);
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array('code' => 0, 'msg' => $response->error->msg);
    }

    /**
     * 修改模板
     */
    public static function modify($data)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/modify';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, $data);
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array('code' => 0, 'msg' => $response->error->msg);
    }

//    /**
//     * 根据 id
//     * 更新模板缩略图、视频2个链接
//     * thumb、linkType、link
//     */
//    public static function modify2Link($data)
//    {
//        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/modify2link';
//        $curl = new Curl();
//        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
//        $curl->post($apiUrl, $data);
//        $response = json_decode($curl->response);
//        if ($response->error->code != 0) {
//            return array('code' => -1, 'msg' => $response->error->msg);
//        }
//        return array('code' => 0, 'msg' => $response->error->msg);
//    }

    /**
     * 根据 id 更新模板缩略图 thumb
     */
    public static function setThumb($data)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/setthumb';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, $data);
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array('code' => 0, 'msg' => $response->error->msg);
    }

    /**
     * 根据 id 更新模板缩略图 linkType、link
     */
    public static function setLink($data)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/setlink';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, $data);
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array('code' => 0, 'msg' => $response->error->msg);
    }

    /**
     * 根据 id 设置 isshow
     */
    public static function setShow($id,$isshow)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/isshow';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'id'    =>  $id,
            'isshow'    =>  $isshow,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array('code' => 0, 'msg' => $response->error->msg);
    }

    /**
     * 设置模板大背景
     */
    public static function setAttr($data)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/setattr';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, $data);
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array('code' => 0, 'msg' => $response->error->msg);
    }

    /**
     * 根据 id 销毁记录
     */
    public static function delete($id)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/delete';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'id'    =>  $id,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array('code' => 0, 'msg' => $response->error->msg);
    }

    /**
     * 清空表
     */
    public static function clear($adminName)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/cleartable';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'adminName' =>  $adminName,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array('code' => 0, 'msg' => $response->error->msg);
    }

    /**
     * 预览动画
     */
    public static function getPreview($id,$isshow=0)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/getpreview';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'id'    =>  $id,
            'isshow'    =>  $isshow,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array(
            'code' => 0,
            'model' => ApiBase::objToArr($response->model),
            'data' => ApiBase::objToArr($response->data),
        );
    }

    /**
     * 获取 model
     */
    public static function getModel()
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/temp/getmodel';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array(
            'code' => 0,
            'model' => ApiBase::objToArr($response->model),
        );
    }
}