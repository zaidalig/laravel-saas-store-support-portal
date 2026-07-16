<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest { public function authorize(): bool { return auth()->check(); } public function rules(): array { return ['order_id'=>'nullable|exists:orders,id','subject'=>'required|string|max:255','message'=>'required|string','priority'=>'required|in:low,medium,high,urgent','status'=>'nullable|in:open,in_progress,waiting_customer,resolved,closed','assigned_to'=>'nullable|exists:users,id']; } }
