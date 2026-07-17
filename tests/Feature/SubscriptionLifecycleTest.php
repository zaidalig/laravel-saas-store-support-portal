<?php

namespace Tests\Feature;

use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function makeCustomer(): User
    {
        return User::create([
            'name' => 'Customer Demo',
            'email' => 'customer-sub@test.local',
            'password' => 'password',
            'role' => 'customer',
            'status' => 'active',
        ]);
    }

    private function makePlan(): PricingPlan
    {
        return PricingPlan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'price' => 49,
            'duration_days' => 30,
            'features' => "Feature A\nFeature B",
            'status' => 'active',
            'display_order' => 1,
        ]);
    }

    public function test_customer_can_subscribe_to_a_pricing_plan(): void
    {
        $customer = $this->makeCustomer();
        $plan = $this->makePlan();

        $response = $this->actingAs($customer)
            ->post(route('dashboard.subscriptions.store', $plan));

        $response->assertRedirect(route('dashboard.subscriptions'));
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $customer->id,
            'pricing_plan_id' => $plan->id,
            'status' => 'active',
        ]);

        $subscription = Subscription::first();
        $this->assertNotNull($subscription->subscription_number);
        $this->assertTrue($subscription->ends_at->greaterThan($subscription->starts_at));
    }

    public function test_customer_can_cancel_own_subscription(): void
    {
        $customer = $this->makeCustomer();
        $plan = $this->makePlan();

        $subscription = Subscription::create([
            'user_id' => $customer->id,
            'pricing_plan_id' => $plan->id,
            'subscription_number' => 'SUB-TEST-00001',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $this->actingAs($customer)
            ->post(route('dashboard.subscriptions.cancel', $subscription))
            ->assertRedirect();

        $subscription->refresh();
        $this->assertSame('cancelled', $subscription->status);
        $this->assertNotNull($subscription->cancelled_at);
    }

    public function test_customer_cannot_cancel_another_users_subscription(): void
    {
        $customer = $this->makeCustomer();
        $other = User::create([
            'name' => 'Other Customer',
            'email' => 'other-sub@test.local',
            'password' => 'password',
            'role' => 'customer',
            'status' => 'active',
        ]);
        $plan = $this->makePlan();

        $subscription = Subscription::create([
            'user_id' => $other->id,
            'pricing_plan_id' => $plan->id,
            'subscription_number' => 'SUB-TEST-00002',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $this->actingAs($customer)
            ->post(route('dashboard.subscriptions.cancel', $subscription))
            ->assertForbidden();
    }

    public function test_admin_can_list_subscriptions(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-sub@test.local',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);
        $customer = $this->makeCustomer();
        $plan = $this->makePlan();

        Subscription::create([
            'user_id' => $customer->id,
            'pricing_plan_id' => $plan->id,
            'subscription_number' => 'SUB-TEST-00003',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.subscriptions.index'))
            ->assertOk()
            ->assertSee('SUB-TEST-00003')
            ->assertSee('Customer Demo');
    }
}
