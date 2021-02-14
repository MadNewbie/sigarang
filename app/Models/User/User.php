<?php

namespace App\Models\User;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Storage;
use Image;
use Intervention\Image\Constraint;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $photo
 */

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    
    const PHOTO_PATH = 'user_data/user/{USER_ID}/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'photo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function saveWithDetails($options = [])
    {   
        $photo = isset($options['photo']) ? $options['photo'] : null;
        $res = true;
        
        $res &= $this->save();
        $res &= $this->savePhoto($photo);
        
        return $res;
    }
    
    private function savePhoto(UploadedFile $photo = null) {
        if (!$photo) {
            return true;
        }
        
        $res = true;
        
        if ($this->photo) {
            Storage::delete($this->photo);
        }
        
        $photoPathRaw = self::PHOTO_PATH;
        $photoPath = str_replace('{USER_ID}', $this->id, $photoPathRaw);
        
        Storage::makeDirectory($photoPath);
        $directory = Storage::path($photoPath);
        $filename = 'profile_pic.' . $photo->getClientOriginalExtension();
        $this->photo = $photoPath . $filename;

        $img = Image::make($photo->getRealPath());
        $img->orientate();
        $img->resize(1000, 1000, function ($constraint) {
                /* @var $constraint Constraint */
                $constraint->aspectRatio();
            });
        $img->save($directory . $filename);
        
        $res = Storage::exists($photoPath . $filename);
        if ($res) {
            if (!$this->save()) {
                $res = false;
                $this->errors[] = "Photo failed";
            }
        } else {
            $res = false;
            $this->errors[] = "Upload photo failed";
        }
        return $res;
    }
    
}
