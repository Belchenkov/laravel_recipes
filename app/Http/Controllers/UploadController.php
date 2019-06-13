<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
        Storage::disk('public')->put($filename, File::get($file));

        $user->profile_pic = $filename;
        $user->save();

        return redirect('/');
    }
}
