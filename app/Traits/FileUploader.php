<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Storage;
use Image;

trait FileUploader
{
	public function uploadImage(Request $request, $requestName, $filename, $directory)
    {
        $filename = str_slug($filename) . '.' . $request->file($requestName)->getClientOriginalExtension();
        $path = $directory . $filename;
        $image = Image::make($request->file($requestName)->getRealpath());
        $height = $image->height();
        $width = $image->width();
        if ($height < $width) {
            $image->crop($height, $height);
            
        } elseif ($height > $width) {
            $image->crop($width, $width); 
        } 

        Storage::disk('public')->put($path, $image); 
        return $filename;
    }

    public function deleteOldImage($directory, $filename)
    {
    	if (Storage::disk('public')->exists($directory . $filename)) {
            Storage::disk('public')->delete($directory . $filename);
        }
    }
}