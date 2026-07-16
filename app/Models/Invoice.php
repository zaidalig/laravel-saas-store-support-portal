<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['order_id','user_id','invoice_number','issue_date','due_date','subtotal','discount','tax','total','status'];
    protected $casts = ['issue_date'=>'date','due_date'=>'date','subtotal'=>'decimal:2','discount'=>'decimal:2','tax'=>'decimal:2','total'=>'decimal:2'];
    public function order() { return $this->belongsTo(Order::class); }
    public function user() { return $this->belongsTo(User::class); }
}
