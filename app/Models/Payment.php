<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id','user_id','payment_number','amount','payment_method','status','paid_at','notes'];
    protected $casts = ['amount'=>'decimal:2','paid_at'=>'datetime'];
    public function order() { return $this->belongsTo(Order::class); }
    public function user() { return $this->belongsTo(User::class); }
}
