<?php
/**
 * Cellular Faremwork
 * 文件读写操作
 * @copyright Cellular Team
 */

namespace core;


class IO
{
    /**
     * 创建目录
     * @param $dir
     * @return bool
     */
    public function creatDir($dir)
    {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0775, true)) return false;
        }
        return true;
    }
    /**
     * 快速获取文件内容
     * @param $file
     * @return bool|string
     */
    public function get($file)
    {
        if(is_file($file)){
            return file_get_contents($file);
        }
        return false;
    }

    /**
     * 读取文件内容
     * @param $file
     * @return bool|string
     */
    public function read($file)
    {
        if(is_file($file)){
            $fp = fopen($file, 'a+'); //打开文件,指定文件路径，读取方式
            $content = fread($fp, filesize($file)); //读取长度
            fclose($fp);
            return $content;
        }
        return false;
    }

    /**
     * 读取大型文件内容
     * @param $file
     * @param int $buffer
     * @return bool|string
     */
    public function readBig($file, $buffer = 1024)
    {
        if(is_file($file)){
            $fp = fopen($file, 'a+');
            $content = '';
            while(!feof($fp)){
                $content .= fread($fp, $buffer);
            }
            fclose($fp);
            return $content;
        }
        return false;
    }

    /**
     * 创建并写入文件内容
     * @param $file
     * @param $content
     */
    public function creat($file, $content)
    {

    }

    /**
     * 写入文件内容
     * @param $file
     * @param $content
     * @param string $node [end:写入文件结尾; start:写入文件开头; cover:覆盖文件内容]
     */
    public function write($file, $content, $node = 'end')
    {

    }

    /**
     * 移动文件
     * @param $current
     * @param $path
     * @param null $name
     * @return bool
     */
    public function move($current, $path, $name = null)
    {
        if(is_file($current)){
            if ($name == null) $name = basename($current);
            if (rename($current, $path.$name)) return true;
        }
        return false;
    }

    /**
     * 删除文件
     * @param $file
     * @return bool
     */
    public function delete($file)
    {
        if (@unlink($file)) return true;
        return false;
    }
}