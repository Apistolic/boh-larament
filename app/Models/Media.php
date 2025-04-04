<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_name',
        'mime_type',
        'size',
        'collection',
        'disk',
    ];

    protected $appends = [
        'url',
    ];


    public function getUrlAttribute(): string
    {
        // Disable debugbar when getting URL to prevent memory issues with large files
        if (class_exists('Debugbar')) {
//            Debugbar::disable();
        }
        
        $url = Storage::disk($this->disk)->url($this->file_name);
        
        if (class_exists('Debugbar')) {
  //          Debugbar::enable();
        }
        
        return $url;
    }

    public function delete()
    {
        Storage::disk($this->disk)->delete($this->file_name);
        return parent::delete();
    }

}
