<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.local',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin)->get('/admin')->assertStatus(200);
    }

    public function test_customer_cannot_access_admin_panel(): void
    {
        $customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@test.local',
            'password' => 'password',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->actingAs($customer)->get('/admin')->assertForbidden();
    }

    public function test_admin_module_pages_render(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        foreach (['/admin/users', '/admin/products', '/admin/orders', '/admin/invoices', '/admin/support-tickets'] as $path) {
            $this->actingAs($admin)->get($path)->assertStatus(200);
        }
    }

    public function test_customer_dashboard_pages_render(): void
    {
        $this->seed();

        $customer = User::where('role', 'customer')->firstOrFail();

        foreach (['/dashboard', '/dashboard/orders', '/dashboard/invoices', '/dashboard/payments', '/dashboard/tickets', '/dashboard/profile'] as $path) {
            $this->actingAs($customer)->get($path)->assertStatus(200);
        }
    }
}
