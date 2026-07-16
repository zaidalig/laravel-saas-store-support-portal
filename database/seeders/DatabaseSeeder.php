<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PricingPlan;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SupportTicket;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create(['name'=>'Admin User','email'=>'admin@example.com','password'=>'password','role'=>'admin','status'=>'active']);
        $staff = collect(range(1,2))->map(fn($i)=>User::create(['name'=>"Staff {$i}",'email'=>"staff{$i}@example.com",'password'=>'password','role'=>'staff','status'=>'active']));
        $customers = collect(range(1,5))->map(fn($i)=>User::create(['name'=>"Customer {$i}",'email'=>"customer{$i}@example.com",'password'=>'password','role'=>'customer','status'=>'active']));

        collect(['site_name'=>'SaaS Store Portal','site_email'=>'support@example.com','site_phone'=>'+1 555 1000','tax_percentage'=>'8','invoice_prefix'=>'INV','order_prefix'=>'ORD','ticket_prefix'=>'TCK'])
            ->each(fn($v,$k)=>Setting::create(['key'=>$k,'value'=>$v,'type'=>is_numeric($v)?'number':'text']));

        $teams = collect(['Sales Support','Product Delivery','Customer Success'])->map(fn($name)=>Team::create(['name'=>$name,'description'=>"{$name} team",'status'=>'active']));
        foreach ($teams as $idx=>$team) {
            TeamMember::create(['team_id'=>$team->id,'user_id'=>$staff[$idx % 2]->id,'position'=>'Lead','status'=>'active']);
            TeamMember::create(['team_id'=>$team->id,'user_id'=>$admin->id,'position'=>'Manager','status'=>'active']);
        }

        $categories = collect(['SaaS Tools','Automation','Design','Hosting','Consulting'])->map(fn($name)=>Category::create(['name'=>$name,'slug'=>Str::slug($name),'description'=>"{$name} services",'status'=>'active']));
        $products = collect(range(1,10))->map(function($i) use ($categories) {
            $name = "Service Package {$i}";
            return Product::create(['category_id'=>$categories[($i-1)%5]->id,'name'=>$name,'slug'=>Str::slug($name),'short_description'=>'Professional SaaS service package.','description'=>"Detailed implementation and support for {$name}.",'price'=>99 + ($i*25),'billing_type'=>['one_time','monthly','yearly'][$i%3],'status'=>'active']);
        });
        collect([['Starter',49,30],['Growth',149,30],['Scale',399,365]])->each(fn($p,$i)=>PricingPlan::create(['name'=>$p[0],'slug'=>Str::slug($p[0]),'price'=>$p[1],'duration_days'=>$p[2],'features'=>"Product access\nEmail support\nInvoice tracking",'status'=>'active','display_order'=>$i+1]));

        foreach (range(1,10) as $i) {
            $customer = $customers[($i-1)%5];
            $product = $products[($i-1)%10];
            $subtotal = $product->price;
            $tax = round($subtotal * .08, 2);
            $order = Order::create(['user_id'=>$customer->id,'order_number'=>'ORD-20260525-'.str_pad((string)$i,5,'0',STR_PAD_LEFT),'customer_name'=>$customer->name,'customer_email'=>$customer->email,'customer_phone'=>'+1 555 20'.$i,'subtotal'=>$subtotal,'discount'=>0,'tax'=>$tax,'total'=>$subtotal+$tax,'status'=>['pending','confirmed','processing','completed'][$i%4],'payment_status'=>$i%3===0?'paid':'unpaid','notes'=>'Sample order']);
            $order->items()->create(['product_id'=>$product->id,'product_name'=>$product->name,'quantity'=>1,'unit_price'=>$product->price,'total'=>$subtotal]);
            $invoice = Invoice::create(['order_id'=>$order->id,'user_id'=>$customer->id,'invoice_number'=>'INV-20260525-'.str_pad((string)$i,5,'0',STR_PAD_LEFT),'issue_date'=>now()->subDays($i),'due_date'=>now()->addDays(14),'subtotal'=>$subtotal,'discount'=>0,'tax'=>$tax,'total'=>$subtotal+$tax,'status'=>$i%3===0?'paid':'unpaid']);
            if ($i%3===0) Payment::create(['order_id'=>$order->id,'user_id'=>$customer->id,'payment_number'=>'PAY-20260525-'.str_pad((string)$i,5,'0',STR_PAD_LEFT),'amount'=>$order->total,'payment_method'=>'card','status'=>'completed','paid_at'=>now()->subDays($i),'notes'=>'Seed payment']);
            $ticket = SupportTicket::create(['user_id'=>$customer->id,'order_id'=>$order->id,'ticket_number'=>'TCK-20260525-'.str_pad((string)$i,5,'0',STR_PAD_LEFT),'subject'=>"Help with order {$order->order_number}",'message'=>'I need help with this order.','priority'=>['low','medium','high','urgent'][$i%4],'status'=>['open','in_progress','waiting_customer','resolved'][$i%4],'assigned_to'=>$staff[($i-1)%2]->id]);
            TicketReply::create(['ticket_id'=>$ticket->id,'user_id'=>$staff[($i-1)%2]->id,'message'=>'Thanks, we are checking this for you.','is_internal_note'=>false]);
        }

        foreach(range(1,5) as $i) ContactMessage::create(['name'=>"Lead {$i}",'email'=>"lead{$i}@example.com",'phone'=>'+1 555 30'.$i,'subject'=>'Product question','message'=>'Tell me more about your services.','status'=>'new']);
        foreach(range(1,8) as $i) ActivityLog::create(['user_id'=>$admin->id,'action'=>'Seeded','module'=>'System','description'=>"Seeded sample record {$i}",'ip_address'=>'127.0.0.1','user_agent'=>'Seeder','created_at'=>now()->subMinutes($i)]);
    }
}