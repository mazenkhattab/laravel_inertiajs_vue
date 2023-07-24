<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;




trait FileHandler
{
    private $files;
    /**
     * this function takes a base64 encoded image and store it in the filesystem and return the name of it
     * (ex. 12546735.png) that will be stored in DB
     * @param $file
     * @param $dir
     * @param false $is_base_64
     * @return string
     */
    public function storeFile($file, $dir,$is_base_64=false){
        
        $this->makeDirectory(storage_path('app/public/'.$dir));
        if($is_base_64) {
            $name = $dir . '/' . str_replace([':', '\\', '/', '*'], '', bcrypt(microtime(true))) . '.' . explode('/', explode(':', explode(';', $file)[0])[1])[1];
            Image::make($file)->save(storage_path('app/public/') . $name);
        }
        else{
            $name = $dir . '/' . $file->hashName();
            Image::make($file)->resize(700, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path('app/public/') . $name);
        }
        return $name;
    }
    
    public function storePDF($file, $dir){
        
        $this->makeDirectory(storage_path('app/public/'.$dir));
         $path=substr($file->store('public/'.$dir),7);
        return $path;
    }

    /**
     * this function takes $newImage(base64 encoded) and $oldImage(DB name) ,
     * it deletes the $oldImage from the filesystem and store the $newImage and return it's name that will be stored in DB
     * @param $new_file
     * @param $old_file
     * @param $dir
     * @return string
     */
    public  function updateFile($new_file, $old_file, $dir){
        $this->deleteFile($old_file);
        $name=$this->storeFile($new_file,$dir);
       
        return $name;
    }

    /**
     * this function takes image(DB name) and deletes it from the filesystem ,
     * returns true if deleted and false if not found
     * @param $file
     * @return bool
     */
    public  function deleteFile($file){

        if(file_exists(storage_path('app/public/').$file)){
            Storage::disk('public')->delete($file);
            return true;
        }
        return false;
    }

    /**
     * make directory for files
     * @param $path
     * @return mixed
     */
    private function makeDirectory($path)
    {   
        $this->files = new Filesystem();
        $this->files->makeDirectory($path, 0777, true,true);
        return $path;
    }

    public function storeImageFromUrl($url , $dir){
        
        $this->makeDirectory(storage_path('app/public/'.$dir));
        $name = $dir . '/' . Str::random(16) .'.jpg';
        Image::make($url)->save(storage_path('app/public/').$name);
        return $name;
    }
    public function StoreImageToModel($model,$image) {
        if (is_array($image)) {
            foreach ($image as $index => $one_image) {
                $file = $this->storeImages($one_image,$model);
                $model_image = new Media(['path' => $file, 'order' => $index + 1]);
                $model->images()->save($model_image);
            }
        } else {
            $file = $this->storeImages($image,$model);
            $model_image = new Media([$model->id, 'path' => $file, 'order' => '1']);
            $model->image()->save($model_image);
        }
        return true;
    }

    public function UpdateModelImage($model,$image) {
        if (!is_array($image) ){
            $this->deleteFile($model->image->path);
            Media::destroy($model->image->id);
            $file = $this->storeImages($image, $model);
            $model_image = new Media([$model->id, 'path' => $file, 'order' => '1']);
            $model->image()->save($model_image);
        } elseif(!$model->images())
            return $this->StoreImageToModel($model, $image);
        else  {
            foreach ($image as $index => $model_image) {
                $file = $this->storeImages($model_image, $model);
                $model_image = new Media(['path' => $file]);
                $model->images()->save($model_image);
            }
            foreach ($model->images as $index=>$one_image) {
                $image=Media::find($one_image->id);
                $image->order=$index+1;
                $image->save();
            }
        }
        return true;
    }


    private function storeImages($data,$model ){
        
        $this->makeDirectory(storage_path('app/public/' . $model->getTable()));
        return $this->storeFile($data, $model->getTable());
    }
}

