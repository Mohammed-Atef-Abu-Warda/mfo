<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    // أضف هذا السطر للسماح بحفظ البيانات في هذه الأعمدة
    protected $fillable = [
        'type',
        'price_at_signal',
        'sentiment_score',
        'strength'
    ];
}