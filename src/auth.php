<?php
namespace wxlogin;

use Yii;

class auth{
    
    public static function userInfo($code){
        $token = self::getToken($code);
        if(!$token){
            return false;
        }
        $uinfo = self::getUserInfo($token['access_token'], $token['openid']);
        if(!$uinfo){
            return false;
        }
        return $uinfo;
    }
        
    public static function getUserInfo($token, $openid){
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $token . '&openid=' . $openid . '&lang=zh_CN';
        $re = json_decode(self::postDataCurl($url), true);
        if(isset($re['openid'])){
            return $re;
        }
        return false;
    }
    
    public static function getToken($code){
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . Yii::$app->params['wx_appid'] . '&secret=' . Yii::$app->params['wx_secret'] . '&code=' . $code . '&grant_type=authorization_code';
        $re = json_decode(self::postDataCurl($url), true);
        if(isset($re['access_token'])){
            return ['access_token' => $re['access_token'], 'openid' => $re['openid']];
        }
        return false;
    }
    
    private static function postDataCurl($url, $data = array(), $timeout = 10){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); //服务地址URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //发送的数据
        $rtn = curl_exec($ch); //获得返回值
        curl_close($ch);
        return $rtn;
    }
}