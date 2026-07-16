<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    protected $fillable = ['ticket_id','user_id','message','is_internal_note'];
    protected $casts = ['is_internal_note'=>'boolean'];
    public function ticket() { return $this->belongsTo(SupportTicket::class, 'ticket_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
