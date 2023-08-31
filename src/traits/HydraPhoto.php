<?php

namespace HydraService\traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

trait HydraPhoto
{
    public function storageProvider(){
        return  app('storageProvider');
    }

    public function yetFile(mixed $file, bool $pathOnly, ?string $path = "")
    {
        if (strval($file) == '')
        {
            return $this->fileSrc;
        }

        $pathOnly ? $filePath = $path . '/' . $file : $filePath = $file;

        if (!Storage::disk($this->storageProvider())->has('public/' . $filePath))
        {
            return $file;
        }

        $fileSrc = Storage::disk($this->storageProvider())->url('public' . $filePath);
        // $fileSrc = 'data:image/jpeg:base64,'.base64_encode(Storage::disk($this->storageProvider())->get('public/'.$filePath));
        return $fileSrc;
    }

    public function storePhotos(mixed $photos, string $path, string $name = "photoImage",bool $preview = true,bool $dropQuality = false)
    {

        if (is_array($photos))
        {
            $nameCollection = [];
            foreach ($photos as $key => $photo)
            {
                $photoName = $this->yetPhotoName($name, $photo);
                $nameCollection[$key] = $path . '/' . $photoName;
                $this->putToStorage($path, $photo,$photoName,$preview,$dropQuality);
            }
            return $nameCollection;
        }
        $photoName = $this->yetPhotoName($name, $photos);
        $this->putToStorage($path, $photos,$photoName,$preview,$dropQuality);
        return $path.'/'.$photoName;
    }

    protected function modifyImageRecursively($photo, int $quality) : \Intervention\Image\Image
    {
        $img = Image::make($photo);
        $img->resize(500, null, function ($constraint)
        {
            $constraint->aspectRatio();
        });
        $img->encode('jpg', $quality);
        return $img;
    }

    protected function putToStorage($path, $photo,$photoName,bool $preview = true,bool $dropQuality = false) : void
    {
        $reducedPhoto = $this->modifyImageRecursively($photo, 60);
        $savePhoto = file_get_contents($photo);
        if($dropQuality)
        {
            $photo = $this->modifyImageRecursively($photo, 90);
            $savePhoto = $photo->stream();
        }
        Storage::disk($this->storageProvider())->put('public/' . $path . '/' . $photoName, $savePhoto);
        $preview && Storage::disk($this->storageProvider())->put('public/preview//' . $path . '/'  . $photoName, $reducedPhoto->stream());
    }

    protected function yetPhotoName(string $name, $photo) :string
    {
        return $name . uniqid() . '.' . $photo->getClientOriginalExtension();
    }


}
