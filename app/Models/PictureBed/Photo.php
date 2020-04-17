<?php

namespace App\Models\PictureBed;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    /**
     * 数据库表
     * @var
     */
    protected $table = 'photo';

    /**
     * 时间戳自动更新开关
     * @var
     */
    public $timestamps = true;
}
