<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['name'=>'required|string|max:255','email'=>'required|email','phone'=>'nullable|string|max:50','subject'=>'required|string|max:255','message'=>'required|string']; } }
