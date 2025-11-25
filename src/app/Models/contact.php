<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contact extends Model
{
    use HasFactory;

    // Eloquentが使用するテーブル名を明示的に定義
    protected $table = 'contacts';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'tel',
        'content',
        'image_file_path',
        'image_file_name',
    ];
}