<?php
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Modified by: Yehor Chernyshov
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/
namespace Yehor;
/*
    $image = new SimpleImage();
    $image->load('image.jpg');
    $image->resizeToWidth(250);
    $image->setCompressionValue(75);
    $image->save('image1.jpg');
*/

/**
 * Class SimpleImage
 */
class SimpleImage
{
    const IMAGE_JPEG = 'image/jpeg';
    const IMAGE_JPG = 'image/jpg';
    const IMAGE_GIF = 'image/gif';
    const IMAGE_PNG = 'image/png';

    /**
     * @var
     */
    var $image;
    /**
     * @var
     */
    var $imageType;
    /**
     * @var int
     */
    var $defaultCompression = 50;
    /**
     * @var
     */
    var $compressionValue;
    /**
     * @var int
     */
    var $defaultPermissions = 0666;

    /**
     * @param $filename
     */
    public function load($filename)
    {
        $imageInfo = getimagesize($filename);
        $this->imageType = $imageInfo['mime'];
        if ($this->imageType == self::IMAGE_JPEG || $this->imageType == self::IMAGE_JPG) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->imageType == self::IMAGE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->imageType == self::IMAGE_PNG) {
            $this->image = imagecreatefrompng($filename);
        }
    }

    /**
     * @param $filename
     * @param null $permissions
     */
    public function save($filename, $permissions = null)
    {
        if ($this->imageType == self::IMAGE_JPEG || $this->imageType == self::IMAGE_JPG) {
            $compression = empty($this->compressionValue) ? $this->defaultCompression : $this->compressionValue;
            imagejpeg($this->image, $filename, $compression);
        } elseif ($this->imageType == self::IMAGE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($this->imageType == self::IMAGE_PNG) {
            imagepng($this->image, $filename);
        }
        $permissions = $permissions == null ? $this->defaultPermissions : $permissions;
        chmod($filename, $permissions);
        imagedestroy($this->image);
        $this->image = $this->imageType = $this->compressionValue = null;
    }

    /**
     *
     */
    public function output()
    {
        if ($this->imageType == self::IMAGE_JPEG || $this->imageType == self::IMAGE_JPG) {
            imagejpeg($this->image);
        } elseif ($this->imageType == self::IMAGE_GIF) {
            imagegif($this->image);
        } elseif ($this->imageType == self::IMAGE_PNG) {
            imagepng($this->image);
        }
    }

    /**
     * @param $height
     */
    public function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->image);
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return imagesx($this->image);
    }

    /**
     * @param $width
     * @param $height
     */
    public function resize($width, $height)
    {
        $newImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $newImage;
    }

    /**
     * @param $width
     */
    public function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * @param $scale
     */
    public function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * @param mixed $compressionValue
     * @return $this
     */
    public function setCompressionValue($compressionValue)
    {
        $this->compressionValue = $compressionValue;
        return $this;
    }

}