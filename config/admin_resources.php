<?php

return [
    'users' => [
        'title' => 'Users',
        'model' => App\Models\User::class,
        'label' => 'name',
        'search' => ['name', 'email'],
        'filters' => ['role', 'status'],
        'fields' => [
            'name' => ['type' => 'text', 'required' => true],
            'email' => ['type' => 'email', 'required' => true],
            'password' => ['type' => 'password'],
            'role' => ['type' => 'select', 'options' => ['admin' => 'Admin', 'staff' => 'Staff', 'customer' => 'Customer'], 'required' => true],
            'status' => ['type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'required' => true],
        ],
        'rules' => ['name'=>'required|string|max:255','email'=>'required|email|max:255','password'=>'nullable|string|min:8','role'=>'required|in:admin,staff,customer','status'=>'required|in:active,inactive'],
        'columns' => ['name', 'email', 'role', 'status'],
    ],
    'teams' => [
        'title' => 'Teams', 'model' => App\Models\Team::class, 'label' => 'name', 'search' => ['name', 'description'], 'filters' => ['status'],
        'fields' => ['name'=>['type'=>'text','required'=>true], 'description'=>['type'=>'textarea'], 'status'=>['type'=>'select','options'=>['active'=>'Active','inactive'=>'Inactive']]],
        'rules' => ['name'=>'required|string|max:255','description'=>'nullable|string','status'=>'required|in:active,inactive'],
        'columns' => ['name','description','status'],
    ],
    'team-members' => [
        'title' => 'Team Members', 'model' => App\Models\TeamMember::class, 'label' => 'position', 'search' => ['position'], 'filters' => ['team_id','status'],
        'relations' => ['team','user'],
        'fields' => ['team_id'=>['type'=>'relation','model'=>App\Models\Team::class,'label'=>'name','required'=>true], 'user_id'=>['type'=>'relation','model'=>App\Models\User::class,'label'=>'email','required'=>true], 'position'=>['type'=>'text'], 'status'=>['type'=>'select','options'=>['active'=>'Active','inactive'=>'Inactive']]],
        'rules' => ['team_id'=>'required|exists:teams,id','user_id'=>'required|exists:users,id','position'=>'nullable|string|max:255','status'=>'required|in:active,inactive'],
        'columns' => ['team.name','user.email','position','status'],
    ],
    'categories' => [
        'title' => 'Categories', 'model' => App\Models\Category::class, 'label' => 'name', 'search' => ['name','slug'], 'filters' => ['status'],
        'fields' => ['name'=>['type'=>'text','required'=>true], 'slug'=>['type'=>'text','required'=>true], 'description'=>['type'=>'textarea'], 'status'=>['type'=>'select','options'=>['active'=>'Active','inactive'=>'Inactive']]],
        'rules' => ['name'=>'required|string|max:255','slug'=>'required|string|max:255','description'=>'nullable|string','status'=>'required|in:active,inactive'],
        'columns' => ['name','slug','status'],
    ],
    'products' => [
        'title' => 'Products/Services', 'model' => App\Models\Product::class, 'label' => 'name', 'search' => ['name','slug','short_description'], 'filters' => ['category_id','status'], 'relations' => ['category'],
        'fields' => ['category_id'=>['type'=>'relation','model'=>App\Models\Category::class,'label'=>'name'], 'name'=>['type'=>'text','required'=>true], 'slug'=>['type'=>'text','required'=>true], 'short_description'=>['type'=>'text'], 'description'=>['type'=>'textarea'], 'price'=>['type'=>'number','step'=>'0.01'], 'billing_type'=>['type'=>'select','options'=>['one_time'=>'One Time','monthly'=>'Monthly','yearly'=>'Yearly']], 'status'=>['type'=>'select','options'=>['active'=>'Active','inactive'=>'Inactive']]],
        'rules' => ['category_id'=>'nullable|exists:categories,id','name'=>'required|string|max:255','slug'=>'required|string|max:255','short_description'=>'nullable|string|max:255','description'=>'nullable|string','price'=>'required|numeric|min:0','billing_type'=>'required|in:one_time,monthly,yearly','status'=>'required|in:active,inactive'],
        'columns' => ['name','category.name','price','billing_type','status'],
    ],
    'pricing-plans' => [
        'title' => 'Pricing Plans', 'model' => App\Models\PricingPlan::class, 'label' => 'name', 'search' => ['name','slug'], 'filters' => ['status'],
        'fields' => ['name'=>['type'=>'text','required'=>true], 'slug'=>['type'=>'text','required'=>true], 'price'=>['type'=>'number','step'=>'0.01'], 'duration_days'=>['type'=>'number'], 'features'=>['type'=>'textarea'], 'status'=>['type'=>'select','options'=>['active'=>'Active','inactive'=>'Inactive']], 'display_order'=>['type'=>'number']],
        'rules' => ['name'=>'required|string|max:255','slug'=>'required|string|max:255','price'=>'required|numeric|min:0','duration_days'=>'required|integer|min:1','features'=>'nullable|string','status'=>'required|in:active,inactive','display_order'=>'required|integer|min:0'],
        'columns' => ['name','price','duration_days','status','display_order'],
    ],
    'contact-messages' => [
        'title' => 'Contact Messages', 'model' => App\Models\ContactMessage::class, 'label' => 'subject', 'search' => ['name','email','subject'], 'filters' => ['status'],
        'fields' => ['name'=>['type'=>'text','required'=>true], 'email'=>['type'=>'email','required'=>true], 'phone'=>['type'=>'text'], 'subject'=>['type'=>'text','required'=>true], 'message'=>['type'=>'textarea'], 'status'=>['type'=>'select','options'=>['new'=>'New','read'=>'Read','replied'=>'Replied','closed'=>'Closed']]],
        'rules' => ['name'=>'required|string|max:255','email'=>'required|email','phone'=>'nullable|string|max:50','subject'=>'required|string|max:255','message'=>'required|string','status'=>'required|in:new,read,replied,closed'],
        'columns' => ['name','email','subject','status'],
    ],
    'settings' => [
        'title' => 'Settings', 'model' => App\Models\Setting::class, 'label' => 'key', 'search' => ['key','value'], 'filters' => ['type'],
        'fields' => ['key'=>['type'=>'text','required'=>true], 'value'=>['type'=>'text'], 'type'=>['type'=>'select','options'=>['text'=>'Text','number'=>'Number','email'=>'Email']]],
        'rules' => ['key'=>'required|string|max:255','value'=>'nullable|string','type'=>'required|in:text,number,email'],
        'columns' => ['key','value','type'],
    ],
];