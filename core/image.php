<?php
/**
 * Cellular Framework
 * 图像编辑类
 * @copyright Cellular Team
 */

namespace core;
use Imagick;


class Image
{
    private $image;
    private $path;

    /**
     * 打开文件
     * @param $file 文件路径
     */
    public function open($file)
    {
        $this->image = new Imagick($file);
        $this->path = $file;
        return $this;
    }

    public function thumb($with, $height)
    {
        $this->image->cropThumbnailImage($with, $height);
        return $this;
    }

    /**
     * 设置图片固定尺寸
     * @param null $with
     * @param null $height
     */
    public function size($with, $height)
    {

    }

    /**
     * 等比缩放图像
     * @param null $with
     * @param null $height
     */
    public function zoom($with = null, $height = null)
    {

    }

    /**
     * 按固定尺寸裁剪
     * @param $with
     * @param $height
     */
    public function sizeCut($with, $height)
    {

    }

    /**
     * 按固定尺寸裁剪
     * @param $ratio
     */
    public function ratioCut($ratio)
    {

    }

    public function save($path = null)
    {
        $path = $path ? $path : $this->path;
        return $this->image->writeImage($path);
    }
}

?>
