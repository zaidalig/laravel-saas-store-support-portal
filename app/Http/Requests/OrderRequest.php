<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest { public function authorize(): bool { return auth()->check(); } public function rules(): array { return ['customer_name'=>'required|string|max:255','customer_email'=>'required|email','customer_phone'=>'nullable|string|max:50','product_id'=>'required|exists:products,id','quantity'=>'required|integer|min:1','notes'=>'nullable|string']; } }
