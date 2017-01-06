<?php
namespace App\Api\ApiUser;

use Curl\Curl;

class ApiUsers
{
    /**
     * 用户接口
     */

    /**
     * 用户登录
     */
    public static function dologin($data)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/user/dologin';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, $data);
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
     * 通过 uid 获取记录
     */
    public static function getOneUser($uid)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/user/show';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'uid'    =>  $uid,
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
     * 通过 uname 获取记录
     */
    public static function getOneUserByUname($uname)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/user/oneuserbyuname';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'uname'    =>  $uname,
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
     * 根据 uid 获取个人信息
     */
    public static function getPersonInfo($uid)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/person/one';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'uid'    =>  $uid,
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
     * 根据 uid 获取公司信息
     */
    public static function getOneCompany($uid)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/company/one';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'uid'    =>  $uid,
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
     * 用户退出，更新日志
     */
    public static function logout($serial)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/log/logout';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'serial'    =>  $serial,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array('code' => 0, 'msg' => $response->error->msg);
    }

    /**
     * 日志增加记录
     */
    public static function addLog($data)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/log/add';
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
     * 用户、管理员退出，更新日志
     */
    public static function modifyLogout($serial)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/log/logout';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'serial'    =>  $serial,
        ));
        $response = json_decode($curl->response);
        if ($response->error->code != 0) {
            return array('code' => -1, 'msg' => $response->error->msg);
        }
        return array('code' => 0, 'msg' => $response->error->msg);
    }

    /**
     * 根据 adminName 获取管理员记录
     */
    public static function getOneAdminByUname($uname)
    {
        $apiUrl = ApiBase::getApiCurl() . '/api/v1/admin/getonebyuname';
        $curl = new Curl();
        $curl->setHeader('X-Authorization', ApiBase::getApiKey());
        $curl->post($apiUrl, array(
            'username'  =>  $uname,
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