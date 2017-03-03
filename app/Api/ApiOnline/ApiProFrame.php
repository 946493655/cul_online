<?php
namespace App\Api\ApiOnline;

use Curl\Curl;

class ApiProFrame
{
    /**
     * 产品关键帧接口
     */

    /**
     * 通过 layerid 获取某一动画层关键帧
     */
    public static function index($pro_id,$layerid,$attr=0)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/pro/frame';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'pro_id'    =>  $pro_id,
            'layerid'   =>  $layerid,
            'attr'    =>  $attr,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array(
            'code' => 0,
            'data' => ApiBase::objToArr($response->data),
        );
    }

    /**
     * 通过 pro_id 获取产品关键帧集合
     */
    public static function getFramesByProid($pro_id)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/pro/frame/framesbyproid';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'pro_id'    =>  $pro_id,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array(
            'code' => 0,
            'data' => ApiBase::objToArr($response->data),
        );
    }

    /**
     * 获取 model
     */
    public static function getModel()
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/pro/frame/getmodel';
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