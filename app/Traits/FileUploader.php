<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;

class FileUploader {
    public static function upload($file,$folder,$previous_file = NULL)
    {
        if(!empty($previous_file))
        {
            $path = storage_path().'/app/public/'.$previous_file;
            if(file_exists($path))
            {
                unlink($path);
            }
        }

        $filepath = '/'.$folder.'/'.rand().'.'.$file->getClientOriginalExtension();
        Storage::disk('uploads')->put($filepath,file_get_contents($file));

        return $filepath;
    }

    public static function unlinkfile($previous_file)
    {
        if(isset($previous_file))
        {
            $path = storage_path().'/app/public'.$previous_file;
            if(file_exists($path))
            {
                unlink($path);
            }
        }
    }
}