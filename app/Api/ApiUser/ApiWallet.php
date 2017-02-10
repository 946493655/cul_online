<?php
namespace App\Api\ApiUser;

use Curl\Curl;

class ApiWallet
{
    /**
     * 钱包接口
     */

    public static function getWalletByUid($uid)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/wallet/onebyuid';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'uid'   =>  $uid,
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
     * 通过 uid、sign、gold、tip 兑换更新钱包福利
     */
    public static function setConvert($uid,$type,$number)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/wallet/convert';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'uid'  =>  $uid,
            'type'  =>  $type,
            'number'    =>  $number,
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
}