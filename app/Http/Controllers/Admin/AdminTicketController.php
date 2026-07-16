<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = SupportTicket::with('user','assignee')->when($request->search, fn($q,$s)=>$q->where('ticket_number','like',"%{$s}%")->orWhere('subject','like',"%{$s}%"))
            ->when($request->status, fn($q,$s)=>$q->where('status',$s))->when($request->priority, fn($q,$s)=>$q->where('priority',$s))->latest()->paginate(10)->withQueryString();
        return view('admin.tickets.index', ['tickets'=>$tickets, 'staff'=>User::whereIn('role',['admin','staff'])->get()]);
    }
    public function show(SupportTicket $supportTicket) { return view('admin.tickets.show', ['ticket'=>$supportTicket->load('user','order','assignee','replies.user'), 'staff'=>User::whereIn('role',['admin','staff'])->get()]); }
    public function update(Request $request, SupportTicket $supportTicket) { $supportTicket->update($request->validate(['status'=>'required','priority'=>'required','assigned_to'=>'nullable|exists:users,id'])); return back()->with('success','Ticket updated.'); }
    public function reply(Request $request, SupportTicket $supportTicket) { TicketReply::create($request->validate(['message'=>'required|string','is_internal_note'=>'nullable|boolean']) + ['ticket_id'=>$supportTicket->id,'user_id'=>auth()->id()]); return back()->with('success','Reply added.'); }
}