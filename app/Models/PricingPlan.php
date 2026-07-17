<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = ['name','slug','price','duration_days','features','status','display_order'];
    protected $casts = ['price' => 'decimal:2', 'duration_days' => 'integer', 'display_order' => 'integer'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
