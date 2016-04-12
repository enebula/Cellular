<?php
/**
 * Cellular Framework
 * 文件上传类
 * @copyright Cellular Team
 */

namespace core;


class Upload
{
    /**
     * 检查文件类型
     * @param $mime
     * @param $type|array or string
     * @return bool|string
     */
    public function checkMime($mime, $type)
    {
        if ($mime = $this->mime($mime)) {
            if ($type) {
                if (is_array($type)) {
                    if (in_array($mime, $type)) return $mime;
                } else {
                    if ($mime == $type) return $mime;
                }
            }
        }
        return false;
    }

    /**
     * 检查文件大小
     * @param $size|integer 单位kb
     * @param $max
     * @return bool
     */
    public function checkSize($size, $max)
    {
        $max = intval($max) * 1024;
        $size = intval($size);
        if ($size > $max) return false;
        return $size;
    }

    /**
     * 保存文件
     * @param $file|string
     * @param $path|string
     * @param $name|string
     * @return bool
     */
    public function save($tmp, $path, $name)
    {
        //创建路径
        if (!$this->creatDir($path)) return false;
        //保存临时文件到指定目录
        $fpath = $path . $name;
        if (move_uploaded_file($tmp, $fpath)) {
            chmod($fpath, 0755);
            return true;
        }
        return false;
    }

    /**
     * 检查文件类型
     * @param $type|string 文件类型
     * @return bool|string
     */
    private function mime($type)
    {
        switch ($type) {
            case 'image/gif':
                return 'gif';
                break;
            case 'image/pjpeg'://IE6
                return 'jpg';
                break;
            case 'image/jpeg'://IE8,Chrome,FireFox
                return 'jpg';
                break;
            case 'image/x-png'://IE6,IE8
                return 'png';
                break;
            case 'image/png'://chrome,firefox
                return 'png';
                break;
            case 'application/octet-stream'://IE
                return 'mp3';
                break;
            case 'audio/mp3':
                return 'mp3';
                break;
            case 'video/x-flv':
                return 'flv';
                break;
            case 'text/plain':
                return 'txt';
                break;
            case 'application/octet-stream':
                return 'rar';
                break;
            case 'application/x-zip-compressed':
                return 'zip';
                break;
            case 'application/msword':
                return 'doc';
                break;
            default:
                return false;
                break;
        }
    }

    private function creatDir($dir)
    {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0775, true)) return false;
        }
        return true;
    }
}
?>