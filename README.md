
# Hydra Service

This pacakges is for using Storage  with different provider  with easy steps.




## Authors

- [@htetshine](https://github.com/Dede182)


## Installation

Install my-project with npm

```bash
  composer require htetshine/hydra-service
```
You need to init the repository in composer.json

```bash
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/Dede182/HydraService"
        }
    ],
```
## Usage/Examples

getOptions() will return the array of options value to customize
```php
    use HydraService\Services\HydraStorage;
    
    $option = HydraStorage::getOptions();
    //output 
    [
        'preview' =>  true, //store preview photo with eco size
        'dropQuality' =>  false, //reduce the image quality
        'resize' =>  false, //reduce the image size
        'size' =>  500, //adjust the amount of size
        'quality' =>  50, //adjust the quality of image
        'width' =>  0, //width of the image to both image if preivew is true
        'height' =>  0, //same as width
    ]

```
use Where($provider) to store the photo on your spefic HydraStorage
```php
  $option = HydraStorage::getOptions();
  $option['dropQuality'] = false;
   $path = HydraStorage::where('local')->storePhotos($profilePhoto,"profile", 'profile',$option);

```
Use yetFile(mixed $photoName,bool $pathOnly ,string $path) to get the fil back ;
```php
   $profilePhoto = HydraStorage::yetFile($value, true);
```
      
