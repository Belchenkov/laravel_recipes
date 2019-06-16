<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadController extends Controller
{
    public function getUpload()
    {
        return view('upload');
    }

    public function postUpload(Request $request)
    {
        $user = Auth::user();
        $file = $request->file('picture');
        $filename = uniqid($user->id . "_").".".$file->getClientOriginalExtension();
        //Storage::disk('public')->put($filename, File::get($file));
        Storage::disk('s3')->put($filename, File::get($file), 'public');

        $url = Storage::disk('s3')->url($filename);
        //$user->profile_pic = $filename;
        $user->profile_pic = $url;
        $user->save();

        // create the thumbnail and save it
        $thumb = Image::make($file);
        $thumb->fit(200);
        $jpg = (string) $thumb->encode('jpg');

        $thumbName = pathinfo($filename, PATHINFO_FILENAME);
        Storage::disk('s3')->put($thumbName, $jpg, 'public');

        return view('upload-complete')->with('filename', $filename)->with('url', $url);
    }

    public function getThumbnailAttribute()
    {

        $path = pathinfo($this->profile_pic);
        return $path['dirname'] . '/' . $path['filename'] . "-thumb.jpg";
    }
}
