<?php
namespace zh\qiniu;

use yii\helpers\Json;
use yii\helpers\ArrayHelper;
/**
 * @author yanghu <127802495@qq.com>
 */
class Auth
{
    private $accessKey;
    private $secretKey;

    
    // 上传策略
    public $policy = [];
    
    // 上传空间
    public $scope;
    
    // 凭证过期时间
    public $expires = 3600;

    public function __construct($accessKey, $secretKey, $scope)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->scope = $scope;
    }
    
    /**
     * 获取上传策略凭证json字符串
     * @return string
     */
    public function getPolicyJson()
    {
        return Json::encode(ArrayHelper::merge([
            'scope' => $this->scope,
            'deadline' => time() + $this->expires
        ], $this->policy));
    }

    /**
     * 获取上传凭证
     * @return string
     */
    public function uploadToken()
    {
        $data = $this->_base64_urlSafeEncode($this->getPolicyJson());
        return $this->accessKey . ':' . $this->sign($data) . ':' . $data;
    }
    
    /**
     * 签名算法
     * @param string $data
     * @return string
     */
    public function sign($data)
    {
        return $this->_base64_urlSafeEncode(hash_hmac('sha1', $data, $this->secretKey, true));
    }

    /**
     * 对提供的数据进行urlsafe的base64编码。
     * @param string $data 待编码的数据，一般为字符串
     * @return string 编码后的字符串
     * @link http://developer.qiniu.com/docs/v6/api/overview/appendix.html#urlsafe-base64
     */
    private function _base64_urlSafeEncode($data)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }
}