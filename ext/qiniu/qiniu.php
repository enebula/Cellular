<?php
/**
 * Cellular Framework
 * 七牛接口
 * @copyright Cellular Team
 */
namespace ext\qiniu;
use Cellular;
final class Qiniu
{
    private $ak; # AccessKey
    private $sk; # SecretKey
    private $bucket; # 存储空间
    private $token;

    public function __construct()
    {
        $config = Cellular::config('qiniu');
        $this->ak = $config['ak'];
        $this->sk = $config['sk'];
        $this->bucket = $config['bucket'];
        $this->token = self::token();
    }

    private function token()
    {

    }
}