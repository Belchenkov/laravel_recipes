<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $file = $request->file('picture');
        Storage::disk('public')->put($file->getClientOriginalName(), File::get($file));

        return redirect('/');
    }
}
