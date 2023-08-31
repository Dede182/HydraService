<?php

namespace HydraService\Services;

use HydraService\Exceptions\HydraFileNotBind;
use Intervention\Image\ImageManagerStatic as Image;

class HydraModify
{
    protected static $photo;

    protected int $quality = 50;

    protected int $size = 500;

    protected int $width = 0 ;

    protected int $height = 0 ;

    public static function modify($photo)
    {
        self::$photo = $photo;
        return new self(); // Return a new instance to support method chaining
    }

    protected function modifyImageRecursively(): \Intervention\Image\Image
    {
        $photo = self::isBinded();
        $img = Image::make($photo);

        if(($this->width > 0 ) && ($this->height > 0)){
            $img->crop($this->width, $this->height);
        }

        $img->resize($this->size, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->encode('jpg', $this->quality);
        return $img;
    }

    protected static function isBinded()
    {
        if (self::$photo) {
            return self::$photo;
        } else {
            throw new HydraFileNotBind();
        }
    }

    public function modifySize(int $size)
    {
        $this->size = $size;
        return $this; // Return $this for method chaining
    }

    public function modifyQuality(int $quality)
    {
        $this->quality = $quality;
        return $this; // Return $this for method chaining
    }

    public function modifyImage()
    {
        return $this->modifyImageRecursively();
    }

    public function modifyWidthAndHeight($width,$height){
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

}
