<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest; use Illuminate\Validation\Rule;

class UserRequest extends FormRequest { public function authorize(): bool { return $this->user()?->isAdminUser() ?? false; } public function rules(): array { $id=$this->route('id') ?? $this->route('user')?->id; return ['name'=>'required|string|max:255','email'=>['required','email','max:255',Rule::unique('users','email')->ignore($id)],'password'=>[$this->isMethod('post')?'required':'nullable','string','min:8'],'role'=>'required|in:admin,staff,customer','status'=>'required|in:active,inactive']; } }
