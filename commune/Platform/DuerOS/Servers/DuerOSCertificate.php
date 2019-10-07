<?php

/**
 * Class DuerOSCertificate
 * @package Commune\Platform\DuerOS\Servers
 */

namespace Commune\Platform\DuerOS\Servers;

use Psr\Log\LoggerInterface;

/**
 * todo 未来还是要和 bot-sdk同步才行.
 *
 * 由于 DuerOS 的 bot-sdk 自己的 certificate 用了大量private方法
 * 同时又用了全局变量, 导致在swoole 里不兼容, 又无法 hack
 *
 * 所以我特别反对把 java 的习惯带到 php. 用private 作为默认
 * 只有特别明白为什么用 private, 才应该用. 否则都默认是 protected
 *
 */
class DuerOSCertificate
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $verifyRequestSign = false;

    /**
     * @var string
     */
    protected $privateKeyContent;

    /**
     * @var array
     */
    protected $server;

    /**
     * @var string
     */
    protected $rawInput;

    /**
     * @var string[]
     */
    protected static $publicKeyValues = [];

    const URL_SCHEME = 'https';
    const URL_HOST = 'duer.bdstatic.com';

    /**
     * DuerOSCertificate constructor.
     * @param LoggerInterface $logger
     * @param string $privateKeyContent
     * @param array $server
     * @param string $rawInput
     */
    public function __construct(
        LoggerInterface $logger,
        string $privateKeyContent,
        array $server,
        string $rawInput
    ) {
        $this->logger = $logger;
        $this->privateKeyContent = $privateKeyContent;
        $this->rawInput = $rawInput;
        $this->server = $server;
    }

    /**
     * 开启验证请求参数签名，阻止非法请求
     *
     */
    public function enableVerifyRequestSign() : void
    {
        $this->verifyRequestSign = true;
    }

    /**
     * 关闭验证请求参数签名
     *
     */
    public function disableVerifyRequestSign() : void
    {
        $this->verifyRequestSign = false;
    }

    /**
     * @desc 判断是否是百度域
     * @param string $url
     * @return bool
     */
    public static function isBaiduDomain($url) : bool
    {
        $array = parse_url($url);
        $scheme = isset($array['scheme']) ? $array['scheme'] : '';
        $host = isset($array['host']) ? $array['host'] : '';

        if($scheme == self::URL_SCHEME && $host == self::URL_HOST){
            return true;
        }
        return false;
    }

    public function getSignatureCertUrl() : string
    {
        return $this->server['HTTP_SIGNATURECERTURL'] ?? '';
    }

    /**
     * @param null
     * @return resource|null
     */
    protected function getRequestPublicKey()
    {
        $filename = $this->getSignatureCertUrl();
        if(!self::isBaiduDomain($filename) || empty($filename)) {
            return null;
        }

        if (isset(self::$publicKeyValues[$filename])) {
            $content = self::$publicKeyValues[$filename];

        } else {
            $content = file_get_contents($filename);
            if (empty($content)) {
                return null;
            }

            self::$publicKeyValues[$filename] = $content;
        }

        return openssl_pkey_get_public($content);
    }

    /**
     * @desc 验证请求者是否合法
     * @param null
     * @return boolean
     */
    public function verifyRequest()
    {
        if(empty($this->privateKeyContent) || !$this->verifyRequestSign) {
            $this->warning('is close');
            return true;
        }

        $publicKey = $this->getRequestPublicKey();
        if(empty($publicKey)) {
            $this->warning('public key missing');
            return false;
        }
        if (empty($this->rawInput)) {
            $this->warning('input is empty');
            return false;
        }
        $sig = $this->getRequestSig();
        if (empty($sig)) {
            $this->warning('sig is empty');
            return false;
        }

        // 公钥解密
        $verify = openssl_verify(
            $this->rawInput,
            base64_decode($sig),
            $publicKey,
            OPENSSL_ALGO_SHA1
        );

        if ($verify != 1) {
            $this->warning("duerOSVerify openssl verify failed with value $verify");
            return false;
        }
        return true;
    }

    /**
     * 生成签名，当使用DuerOS统计功能或者推送消息
     * @param string $content 待签名内容
     * @return string|null
     */
    public function getSig(string $content) : ? string
    {
        if(empty($this->privateKeyContent) || empty($content)) {
            return null;
        }
        $privateKey = openssl_pkey_get_private($this->privateKeyContent, '');
        $encryptedData = '';
        // 私钥加密
        openssl_sign($content, $encryptedData, $privateKey, OPENSSL_ALGO_SHA1);
        return base64_encode($encryptedData);
    }

    /**
     * @return string
     */
    public function getRequestSig() : string
    {
        return  $this->server['HTTP_SIGNATURE'] ?? '';
    }

    protected function warning(string $message) : void
    {
        $this->logger->warning("duerOSVerify $message");
    }
}