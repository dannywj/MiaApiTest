<?php
/**
 * Rsa
 *
 * @package
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author Tobias Schlitt <toby@php.net>
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class Rsa {

    private $_publicKey;

    private $_privateKey;


    private $config_SERVICE_PRIVATE_KEY = "
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCiwJbb2EeK9ZB4Chyj5/mIVPREod0pJrv3LM2UVtkod+2mPVjV
9Xi1E06gUaoexX/ebfRXm1eBwu3LtYbklh5Ji5oFycoUCFhwzhmm8ZtjdkCIicFf
xUU4I5NunL6+37+hy43EgCrao5tFgHtnkeR/vNyGfaxdxevPbVEtWlJz6wIDAQAB
AoGAVwePZC4qS6d2won9uLQiXoG3QUAhCJFa8Bj4MbujUh2Xak7hw0AJdSLG57nj
s6K+9s1rXLGHwK7hBA6k/HU96hG5QUqM61MjwhTe+XPJvGkGzXT8ymlG0k6h+L59
0WWm7TVXT07+v63Zot5pRz9+ivl4MK9Dr400Ym5gjVQeg0kCQQDOLWsXYAD0eOc2
QxjQH1p+FM3dsmmZF/O2k57LJHV2qiSitQloOUJ1W6KpM3ji0Tl0yDNfU8CiX3Ab
5IN84bpnAkEAyhTMonV8VVqJINkpIpQw3Pkf6YNAqtlq3jzZRnR4WwjFqJ65qNfa
uU89zJ9AD+N5pkR6N+N+7TcKCt+5neOP3QJAbG6gisuX1PsdBoGlNBe5POPuHTFu
rfBV4Wijs8y55i23VMcHaoPqutP1qS0D364PnKaJthHTFtJAoLq+mFgS8wJAUcrF
YOQopOt2IWOEMMjGVkpHTl6fqAdEKBt83fV6WW5dgnhsMRjdILAgFVhHt6acsF17
Em/0CdODLw+Ks4tNyQJBAMoZTF2w0wrHnppGVWNoqWGpI+o3rsMVGv284vlAfnni
0yXd6y5xeZv9S2Gn/yu9h1XwXErNgT/3ueSiHNjm/I4=
-----END RSA PRIVATE KEY-----
";

    private $config_CLIENT_PUBLIC_KEY = "
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCiwJbb2EeK9ZB4Chyj5/mIVPRE
od0pJrv3LM2UVtkod+2mPVjV9Xi1E06gUaoexX/ebfRXm1eBwu3LtYbklh5Ji5oF
ycoUCFhwzhmm8ZtjdkCIicFfxUU4I5NunL6+37+hy43EgCrao5tFgHtnkeR/vNyG
faxdxevPbVEtWlJz6wIDAQAB
-----END PUBLIC KEY-----
";


    public function __construct()
    {
        $this->_privateKey = $this->config_SERVICE_PRIVATE_KEY;
        $this->_publicKey = $this->config_CLIENT_PUBLIC_KEY;
    }

    /**
     *返回对应的私钥
     */
    private function _getPrivateKey()
    {
        return openssl_pkey_get_private($this->_privateKey);
    }

    /**
     * 私钥加密
     */
    public function privEncrypt($data)
    {
        if(!is_string($data) || empty($data) ){
            return false;
        }
        return openssl_private_encrypt($data, $encrypted, $this->_getPrivateKey())? base64_encode($encrypted) : null;
    }


    /**
     * 私钥解密
     */
    public function privDecrypt($encrypted)
    {
        if(!is_string($encrypted) || empty($encrypted)){
            return false;
        }
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, $this->_getPrivateKey()))? $decrypted : null;
    }

    /**
     * sectionPrivDecrypt
     * 私钥分段解密（当使用公钥分段加密时，采用私钥分段解密皆可获得所得数据）
     * @param mixed $encrypted
     * @access public
     * @return void
     */
    public function sectionPrivDecrypt($encrypted){
        if(!is_string($encrypted) || empty($encrypted)){
            return false;
        }
        $decryptRes = "";
        $datas = explode('=',$encrypted);
        foreach ($datas as $value)
        {
            $decryptRes = $decryptRes.$this->privDecrypt($value);
        }
        return $decryptRes;
    }

    /**
     *返回对应的公钥
     */
    private function _getPublicKey(){
        return openssl_pkey_get_public($this->_publicKey);
    }

    /**
     * 公钥加密
     */
    public function publicEncrypt($data){
        if(!is_string($data) || empty($data)){
            return false;
        }
        return openssl_public_encrypt($data, $encrypted, $this->_getPublicKey())? base64_encode($encrypted) : null;
    }

    /**
     * sectionPublicEncrypt
     * 公钥分段加密（因为采用1024位加密，最大加密串长度为117,超过加密就会返回false）
     * @param mixed $data
     * @access public
     * @return void
     */
    public function sectionPublicEncrypt($data){
        if(!is_string($data) || empty($data)){
            return false;
        }
        $subParamsLength = strlen($data);
        $cryptRes = '';
        for($i=0; $i<($subParamsLength - $subParamsLength%117)/117+1; $i++)
        {
            $cryptRes = $cryptRes.($this->publicEncrypt(mb_strcut($data, $i*117, 117, 'utf-8')));
        }
        return $cryptRes;
    }

    public function formatKey($key, $type = 'public'){
        if($type == 'public'){
            $begin = "-----BEGIN PUBLIC KEY-----\n";
            $end = "-----END PUBLIC KEY-----";
        }else{
            $begin = "-----BEGIN PRIVATE KEY-----\n";
            $end = "-----END PRIVATE KEY-----";
        }
        //$key = ereg_replace("\s", "", $key);
        $key= preg_replace('/\s/','',$key);
        $str = $begin;
        $str .= substr($key, 0,64);
        $str .= "\n" . substr($key, 64,64);
        $str .= "\n" . substr($key, 128,64);
        $str .= "\n" . substr($key,192,24);
        $str .= "\n" . $end;
        return $str;
    }


}

/* End of file rsa.php */

