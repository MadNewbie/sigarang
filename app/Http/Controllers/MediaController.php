<?php

namespace App\Http\Controllers;

use Response;
use Auth;
use Storage;

class MediaController extends Controller
{
    public function getPhotoProfile()
    {
        $user = Auth::user();
        $photoPath = isset($user->photo) ? $user->photo : 'public/default.png';
        
        $file = Storage::get($photoPath);
        $type = Storage::mimeType($photoPath);
        $size = Storage::size($photoPath);
        $lastModified = gmdate('D, d M Y H:i:s ', Storage::lastModified($photoPath)) . 'GMT';
        return Response::make($file, 200)
                ->header("Cache-control", 'private')
                ->header("Last-modified", $lastModified)
                ->header("Content-Type", $type)
                ->header("Content-Length", $size);
    }
}

