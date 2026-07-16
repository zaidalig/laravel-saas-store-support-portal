<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id','order_number','customer_name','customer_email','customer_phone','subtotal','discount','tax','total','status','payment_status','notes'];
    protected $casts = ['subtotal'=>'decimal:2','discount'=>'decimal:2','tax'=>'decimal:2','total'=>'decimal:2'];
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function invoice() { return $this->hasOne(Invoice::class); }
    public function tickets() { return $this->hasMany(SupportTicket::class); }
}
