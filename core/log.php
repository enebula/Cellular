<?php
/**
 * Cellular Framework
 * 日志类
 * @copyright Cellular Team
 */

namespace core;

class Log
{
    /**
     * 写入日志
     * 在windows中\r\n是换行 在Mac中\r是换行 在Liunx中\n是换行
     * PHP提供了一个常量来匹配不同的操作系统，即：PHP_EOL
     * @param string $name 消息名称（文件名中）
     * @param string $msg  消息内容
     */
    public static function write($name, $msg)
    {
        $path = '.' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path);
        }
        $file = $path . $name . '.' . date('Y-m-d') . '.txt';
        file_put_contents($file, date('H:i:s') . ' ' . $msg . PHP_EOL, FILE_APPEND);
    }
}