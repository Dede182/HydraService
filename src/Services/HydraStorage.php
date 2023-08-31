<?php

namespace HydraService\Services;
use HydraService\Exceptions\HydraFileUploadFail;
use HydraService\Services\HydraModify;
use Illuminate\Support\Facades\Storage;
class HydraStorage
{
    protected static $storageProvider;

    protected static $defaultOptions =
    [
        'preview' =>  true,
        'dropQuality' =>  false,
        'resize' =>  false,
        'size' =>  500,
        'quality' =>  50,
        'width' =>  0,
        'height' =>  0,
    ];

    public static function where($provider)
    {
        $value = $provider ? $provider : app('storageProvider');
         self::$storageProvider = $value;
        return new self(); // Return a new instance to support method chaining
    }

    public static function getOptions()
    {
        return self::$defaultOptions ;
    }

    public static function yetFile(mixed $file, bool $pathOnly, ?string $path = "")
    {
        return (new self())->retrieveFiile($file, $pathOnly, $path);
    }

    public function retrieveFiile(mixed $file, bool $pathOnly, ?string $path = "")
    {
        $provider = self::$storageProvider;
        if (strval($file) == '')
        {
            return 'image/default.png';
        }

        $pathOnly ? $filePath = $path . '/' . $file : $filePath = $file;

        if (!Storage::disk($provider)->has('public/' . $filePath))
        {
            return $file;
        }

        $fileSrc = Storage::disk($provider)->url('public' . $filePath);
        // $fileSrc = 'data:image/jpeg:base64,'.base64_encode(Storage::disk($provider)->get('public/'.$filePath));
        return $fileSrc;
    }

    public static function storePhotos(mixed $photos, string $path, string $name = "photoImage", array $photoOption = null)
    {
        $photoOption = $photoOption ?? self::$defaultOptions;
        try {
            if (is_array($photos)) {
                return  (new self())->storeMultiplePhotos($photos, $path, $name, $photoOption);
            } else {
                return  (new self())->storeSinglePhoto($photos, $path, $name, $photoOption);
            }
        } catch (HydraFileUploadFail $e) {
            throw new HydraFileUploadFail();
        }
    }

    protected  function storeMultiplePhotos(array $photos, string $path, string $name,array $photoOption = [])
    {
        $nameCollection = [];

        foreach ($photos as $key => $photo) {
            $photoName = $this->generatePhotoName($name, $photo);
            $nameCollection[$key] = $path . '/' . $photoName;
            $this->putToStorage($path, $photo, $photoName,$photoOption);
        }

        return $nameCollection;
    }

    protected  function storeSinglePhoto($photo, string $path, string $name,array $photoOption = []) : string
    {
        $photoName = $this->generatePhotoName($name, $photo);
        $this->putToStorage($path, $photo, $photoName,$photoOption);
        return $path . '/' . $photoName;
    }

    protected  function generatePhotoName(string $name, $photo): string
    {
        return $name . uniqid() . '.' . $photo->getClientOriginalExtension();
    }

    protected  function putToStorage($path, $photo,$photoName,array $photoOption = []) : void
    {
        $provider = self::$storageProvider;

        $mainPhoto = file_get_contents($photo);

        $modifiedPhoto = HydraModify::modify($photo);
        if($photoOption['dropQuality'])
        {
            $mainPhoto = $modifiedPhoto->modifyQuality($photoOption['quality']);
        }
        if($photoOption['resize'])
        {
            $mainPhoto = $modifiedPhoto->modifySize($photoOption['size']);
        }
        if($photoOption['width'] || $photoOption['height'])
        {
            $mainPhoto = $modifiedPhoto->modifyWidthAndHeight($photoOption['width'],$photoOption['height']);
        }
        if($photoOption['dropQuality'] || $photoOption['resize'] || $photoOption['width'] || $photoOption['height'])
        {
            $mainPhoto = $modifiedPhoto->modifyImage()->stream();
        }


        $previewImage = HydraModify::modify($photo)->modifyQuality($photoOption['quality'])
        ->modifyWidthAndHeight($photoOption['width'],$photoOption['height'])
        ->modifySize($photoOption['size'])->modifyImage();

        Storage::disk($provider)->put('public/' . $path . '/' . $photoName, $mainPhoto);
        $photoOption['preview'] && Storage::disk($provider)->put('public/preview//' . $path . '/'  . $photoName, $previewImage->stream());
    }

}
