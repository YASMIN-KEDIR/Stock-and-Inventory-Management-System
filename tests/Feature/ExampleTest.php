<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest is redirected to login.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * Test 1. Dashboard renders with status 200.
     */
    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /**
     * Test 2. Add Section Hub renders with status 200.
     */
    public function test_authenticated_user_can_view_add_hub(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/add');

        $response->assertStatus(200);
        $response->assertSee('Add Section');
        $response->assertSee('Add Product');
        $response->assertSee('Add Supplier');
        $response->assertSee('Add Customer');
    }

    /**
     * Test 3. Sell Section (POS) renders with status 200.
     */
    public function test_authenticated_user_can_view_sell_section(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/sales/create');

        $response->assertStatus(200);
        $response->assertSee('Point of Sale');
    }

    /**
     * Test 4. Info Section (Buying Price Total vs Selling Price Total) renders with status 200.
     */
    public function test_authenticated_user_can_view_info_section(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/info');

        $response->assertStatus(200);
        $response->assertSee('Info Section');
        $response->assertSee('Total Buying Price (All)');
        $response->assertSee('Total Selling Price (All)');
    }

    /**
     * Test 5. Financial Reports (Daily, Monthly, Yearly) render with status 200.
     */
    public function test_authenticated_user_can_view_financial_reports(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/financial-reports');

        $response->assertStatus(200);
        $response->assertSee('Financial Report');
        $response->assertSee('Daily Report');
        $response->assertSee('Monthly Report');
        $response->assertSee('Yearly Report');
    }

    /**
     * Test products catalog page renders.
     */
    public function test_authenticated_user_can_view_products(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);
    }

    /**
     * Test users hub renders with customers and suppliers.
     */
    public function test_authenticated_user_can_view_users_hub(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Users');
        $response->assertSee('Customers');
        $response->assertSee('Suppliers');
    }

    /**
     * Test info section stock information tab renders.
     */
    public function test_authenticated_user_can_view_stock_information_in_info(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/info?tab=stock');

        $response->assertStatus(200);
        $response->assertSee('Stock Information');
    }

    /**
     * Test info section sales invoices tab renders.
     */
    public function test_authenticated_user_can_view_sales_invoices_in_info(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/info?tab=invoices');

        $response->assertStatus(200);
        $response->assertSee('Sales Invoices');
    }

    /**
     * Test user can create sale with cash or digital payment (CBE, Telebirr, Abyssinia).
     */
    public function test_user_can_create_sale_with_cash_payment(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);
        $product = Product::create([
            'name' => 'Test Coffee',
            'sku' => 'COF-001',
            'current_stock' => 50,
            'cost_price' => 100,
            'selling_price' => 150,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/sales', [
            'sale_date' => now()->toDateString(),
            'payment_method' => 'CBE',
            'amount_paid' => 300,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 150,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales', [
            'payment_method' => 'CBE',
            'payment_status' => 'PAID',
            'grand_total' => 300,
            'amount_paid' => 300,
        ]);
        $this->assertEquals(48, $product->fresh()->current_stock);
    }

    /**
     * Test user can create credit sale with customer debt.
     */
    public function test_user_can_create_credit_sale_with_customer(): void
    {
        $user = User::factory()->create(['role' => 'ADMIN']);
        $customer = Customer::create([
            'name' => 'Abebe Kebede',
            'phone' => '0911223344',
            'is_active' => true,
        ]);
        $product = Product::create([
            'name' => 'Test Tea',
            'sku' => 'TEA-001',
            'current_stock' => 50,
            'cost_price' => 100,
            'selling_price' => 150,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/sales', [
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateString(),
            'payment_method' => 'CASH',
            'amount_paid' => 50, // Partial payment
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => 150,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales', [
            'customer_id' => $customer->id,
            'payment_status' => 'PARTIALLY_PAID',
            'grand_total' => 150,
            'amount_paid' => 50,
            'remaining_balance' => 100,
        ]);
    }
}
