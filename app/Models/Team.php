<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name','description','status'];

    public function members() { return $this->hasMany(TeamMember::class); }
}
