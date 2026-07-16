<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function index() { $u=auth()->user(); return view('dashboard.index', ['orders'=>$u->orders()->count(),'invoices'=>$u->invoices()->count(),'payments'=>$u->payments()->count(),'tickets'=>$u->supportTickets()->count()]); }
    public function orders() { return view('dashboard.orders.index', ['orders'=>auth()->user()->orders()->latest()->paginate(10)]); }
    public function order(Order $order) { $this->own($order); return view('dashboard.orders.show', ['order'=>$order->load('items','invoice','payments')]); }
    public function invoices() { return view('dashboard.invoices.index', ['invoices'=>auth()->user()->invoices()->latest()->paginate(10)]); }
    public function payments() { return view('dashboard.payments.index', ['payments'=>auth()->user()->payments()->with('order')->latest()->paginate(10)]); }
    public function tickets() { return view('dashboard.tickets.index', ['tickets'=>auth()->user()->supportTickets()->latest()->paginate(10)]); }
    public function ticket(SupportTicket $ticket) { $this->own($ticket); return view('dashboard.tickets.show', ['ticket'=>$ticket->load(['replies'=>fn($q)=>$q->where('is_internal_note',false),'replies.user','order'])]); }
    public function createTicket() { return view('dashboard.tickets.create', ['orders'=>auth()->user()->orders()->latest()->get()]); }
    public function storeTicket(Request $request) { $data=$request->validate(['order_id'=>'nullable|exists:orders,id','subject'=>'required','message'=>'required','priority'=>'required|in:low,medium,high,urgent']); SupportTicket::create($data + ['user_id'=>auth()->id(),'ticket_number'=>next_number(setting('ticket_prefix','TCK')),'status'=>'open']); return redirect()->route('dashboard.tickets')->with('success','Ticket created.'); }
    public function reply(Request $request, SupportTicket $ticket) { $this->own($ticket); TicketReply::create(['ticket_id'=>$ticket->id,'user_id'=>auth()->id(),'message'=>$request->validate(['message'=>'required|string'])['message'],'is_internal_note'=>false]); return back()->with('success','Reply sent.'); }
    public function profile() { return view('dashboard.profile', ['user'=>auth()->user()]); }
    public function updateProfile(ProfileRequest $request) { $data=$request->validated(); if(blank($data['password'])) unset($data['password']); $request->user()->update($data); return back()->with('success','Profile updated.'); }
    private function own($model): void { abort_unless((int) $model->user_id === auth()->id(), 403); }
}