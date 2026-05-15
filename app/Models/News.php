<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    // السماح بإدخال البيانات في جميع الحقول
    protected $guarded = [];

    // العلاقة مع جدول الصفقات (الذي سنستخدمه لاحقاً)
    public function signal()
    {
        return $this->belongsTo(Signal::class);
    }
}