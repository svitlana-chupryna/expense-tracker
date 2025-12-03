<?php

namespace Tests\Feature;

use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseControllerDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test dashboard returns correct structure with no expenses
     */
    public function test_dashboard_returns_empty_data_when_no_expenses(): void
    {
        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200)
            ->assertJson([
                'byCategory' => [],
                'total' => 0.0,
                'byMonth' => [],
            ]);
    }

    /**
     * Test dashboard returns expenses grouped by category
     */
    public function test_dashboard_groups_expenses_by_category(): void
    {
        // Create expenses in different categories
        Expense::create([
            'name' => 'Grocery Shopping',
            'amount' => 150.50,
            'category' => 'Food',
            'date' => '2024-01-15',
        ]);

        Expense::create([
            'name' => 'Restaurant',
            'amount' => 75.25,
            'category' => 'Food',
            'date' => '2024-01-20',
        ]);

        Expense::create([
            'name' => 'Gas',
            'amount' => 50.00,
            'category' => 'Transportation',
            'date' => '2024-01-18',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Verify byCategory structure
        $this->assertIsArray($data['byCategory']);
        $this->assertCount(2, $data['byCategory']);
        
        // Find Food category
        $foodCategory = collect($data['byCategory'])->firstWhere('category', 'Food');
        $this->assertNotNull($foodCategory);
        $this->assertEquals(225.75, $foodCategory['total']);
        
        // Find Transportation category
        $transportCategory = collect($data['byCategory'])->firstWhere('category', 'Transportation');
        $this->assertNotNull($transportCategory);
        $this->assertEquals(50.00, $transportCategory['total']);
        
        // Verify total
        $this->assertEquals(275.75, $data['total']);
    }

    /**
     * Test dashboard returns expenses grouped by month
     */
    public function test_dashboard_groups_expenses_by_month(): void
    {
        // Create expenses in different months
        Expense::create([
            'name' => 'January Expense 1',
            'amount' => 100.00,
            'category' => 'Food',
            'date' => '2024-01-15',
        ]);

        Expense::create([
            'name' => 'January Expense 2',
            'amount' => 50.00,
            'category' => 'Transportation',
            'date' => '2024-01-25',
        ]);

        Expense::create([
            'name' => 'February Expense',
            'amount' => 200.00,
            'category' => 'Food',
            'date' => '2024-02-10',
        ]);

        Expense::create([
            'name' => 'March Expense',
            'amount' => 75.50,
            'category' => 'Entertainment',
            'date' => '2024-03-05',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Verify byMonth structure
        $this->assertIsArray($data['byMonth']);
        $this->assertCount(3, $data['byMonth']);
        
        // Verify months are in order
        $months = collect($data['byMonth'])->pluck('month')->toArray();
        $this->assertEquals(['2024-01', '2024-02', '2024-03'], $months);
        
        // Verify totals per month
        $jan = collect($data['byMonth'])->firstWhere('month', '2024-01');
        $this->assertEquals(150.00, $jan['total']);
        
        $feb = collect($data['byMonth'])->firstWhere('month', '2024-02');
        $this->assertEquals(200.00, $feb['total']);
        
        $mar = collect($data['byMonth'])->firstWhere('month', '2024-03');
        $this->assertEquals(75.50, $mar['total']);
    }

    /**
     * Test dashboard filters by start_date
     */
    public function test_dashboard_filters_by_start_date(): void
    {
        Expense::create([
            'name' => 'Old Expense',
            'amount' => 100.00,
            'category' => 'Food',
            'date' => '2024-01-01',
        ]);

        Expense::create([
            'name' => 'Recent Expense',
            'amount' => 200.00,
            'category' => 'Food',
            'date' => '2024-02-01',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats?start_date=2024-01-15');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Should only include February expense
        $this->assertEquals(200.00, $data['total']);
        $this->assertCount(1, $data['byMonth']);
    }

    /**
     * Test dashboard filters by end_date
     */
    public function test_dashboard_filters_by_end_date(): void
    {
        Expense::create([
            'name' => 'Early Expense',
            'amount' => 100.00,
            'category' => 'Food',
            'date' => '2024-01-01',
        ]);

        Expense::create([
            'name' => 'Late Expense',
            'amount' => 200.00,
            'category' => 'Food',
            'date' => '2024-03-01',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats?end_date=2024-02-01');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Should only include January expense
        $this->assertEquals(100.00, $data['total']);
        $this->assertCount(1, $data['byMonth']);
    }

    /**
     * Test dashboard filters by date range
     */
    public function test_dashboard_filters_by_date_range(): void
    {
        Expense::create([
            'name' => 'Before Range',
            'amount' => 50.00,
            'category' => 'Food',
            'date' => '2024-01-01',
        ]);

        Expense::create([
            'name' => 'In Range 1',
            'amount' => 100.00,
            'category' => 'Food',
            'date' => '2024-02-15',
        ]);

        Expense::create([
            'name' => 'In Range 2',
            'amount' => 150.00,
            'category' => 'Transportation',
            'date' => '2024-02-20',
        ]);

        Expense::create([
            'name' => 'After Range',
            'amount' => 75.00,
            'category' => 'Food',
            'date' => '2024-03-15',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats?start_date=2024-02-01&end_date=2024-02-28');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Should only include February expenses
        $this->assertEquals(250.00, $data['total']);
        $this->assertCount(1, $data['byMonth']);
        $this->assertEquals('2024-02', $data['byMonth'][0]['month']);
    }

    /**
     * Test dashboard handles empty start_date parameter
     */
    public function test_dashboard_handles_empty_start_date(): void
    {
        Expense::create([
            'name' => 'Expense',
            'amount' => 100.00,
            'category' => 'Food',
            'date' => '2024-01-15',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats?start_date=');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Should include all expenses when start_date is empty
        $this->assertEquals(100.00, $data['total']);
    }

    /**
     * Test dashboard handles empty end_date parameter
     */
    public function test_dashboard_handles_empty_end_date(): void
    {
        Expense::create([
            'name' => 'Expense',
            'amount' => 100.00,
            'category' => 'Food',
            'date' => '2024-01-15',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats?end_date=');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Should include all expenses when end_date is empty
        $this->assertEquals(100.00, $data['total']);
    }

    /**
     * Test dashboard returns float values for amounts
     */
    public function test_dashboard_returns_float_values(): void
    {
        Expense::create([
            'name' => 'Expense',
            'amount' => 100,
            'category' => 'Food',
            'date' => '2024-01-15',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Verify all amounts are floats
        $this->assertIsFloat($data['total']);
        $this->assertIsFloat($data['byCategory'][0]['total']);
        $this->assertIsFloat($data['byMonth'][0]['total']);
    }

    /**
     * Test dashboard handles decimal amounts correctly
     */
    public function test_dashboard_handles_decimal_amounts(): void
    {
        Expense::create([
            'name' => 'Expense 1',
            'amount' => 10.50,
            'category' => 'Food',
            'date' => '2024-01-15',
        ]);

        Expense::create([
            'name' => 'Expense 2',
            'amount' => 20.75,
            'category' => 'Food',
            'date' => '2024-01-20',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Verify precise decimal handling
        $this->assertEquals(31.25, $data['total']);
        $this->assertEquals(31.25, $data['byCategory'][0]['total']);
    }

    /**
     * Test dashboard categories are sorted alphabetically
     */
    public function test_dashboard_sorts_categories_alphabetically(): void
    {
        Expense::create([
            'name' => 'Expense 1',
            'amount' => 100.00,
            'category' => 'Transportation',
            'date' => '2024-01-15',
        ]);

        Expense::create([
            'name' => 'Expense 2',
            'amount' => 150.00,
            'category' => 'Food',
            'date' => '2024-01-20',
        ]);

        Expense::create([
            'name' => 'Expense 3',
            'amount' => 75.00,
            'category' => 'Entertainment',
            'date' => '2024-01-25',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Verify categories are sorted
        $categories = collect($data['byCategory'])->pluck('category')->toArray();
        $this->assertEquals(['Entertainment', 'Food', 'Transportation'], $categories);
    }

    /**
     * Test dashboard months are sorted chronologically
     */
    public function test_dashboard_sorts_months_chronologically(): void
    {
        Expense::create([
            'name' => 'March Expense',
            'amount' => 100.00,
            'category' => 'Food',
            'date' => '2024-03-15',
        ]);

        Expense::create([
            'name' => 'January Expense',
            'amount' => 150.00,
            'category' => 'Food',
            'date' => '2024-01-15',
        ]);

        Expense::create([
            'name' => 'February Expense',
            'amount' => 75.00,
            'category' => 'Food',
            'date' => '2024-02-15',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Verify months are sorted chronologically
        $months = collect($data['byMonth'])->pluck('month')->toArray();
        $this->assertEquals(['2024-01', '2024-02', '2024-03'], $months);
    }

    /**
     * Test dashboard handles multiple expenses on same date
     */
    public function test_dashboard_handles_multiple_expenses_same_date(): void
    {
        Expense::create([
            'name' => 'Morning Expense',
            'amount' => 50.00,
            'category' => 'Food',
            'date' => '2024-01-15',
        ]);

        Expense::create([
            'name' => 'Afternoon Expense',
            'amount' => 75.00,
            'category' => 'Food',
            'date' => '2024-01-15',
        ]);

        Expense::create([
            'name' => 'Evening Expense',
            'amount' => 100.00,
            'category' => 'Transportation',
            'date' => '2024-01-15',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Verify correct aggregation
        $this->assertEquals(225.00, $data['total']);
        $this->assertEquals(125.00, collect($data['byCategory'])->firstWhere('category', 'Food')['total']);
        $this->assertEquals(100.00, collect($data['byCategory'])->firstWhere('category', 'Transportation')['total']);
    }

    /**
     * Test dashboard handles large amounts
     */
    public function test_dashboard_handles_large_amounts(): void
    {
        Expense::create([
            'name' => 'Large Expense',
            'amount' => 999999.99,
            'category' => 'Investment',
            'date' => '2024-01-15',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        $this->assertEquals(999999.99, $data['total']);
    }

    /**
     * Test dashboard handles zero amount expenses
     */
    public function test_dashboard_handles_zero_amount_expenses(): void
    {
        Expense::create([
            'name' => 'Zero Expense',
            'amount' => 0.00,
            'category' => 'Other',
            'date' => '2024-01-15',
        ]);

        Expense::create([
            'name' => 'Normal Expense',
            'amount' => 100.00,
            'category' => 'Food',
            'date' => '2024-01-15',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Should include zero amount expense in count but not affect total meaningfully
        $this->assertEquals(100.00, $data['total']);
        $this->assertCount(2, $data['byCategory']);
    }

    /**
     * Test dashboard with cross-year date range
     */
    public function test_dashboard_handles_cross_year_date_range(): void
    {
        Expense::create([
            'name' => 'December Expense',
            'amount' => 100.00,
            'category' => 'Food',
            'date' => '2023-12-25',
        ]);

        Expense::create([
            'name' => 'January Expense',
            'amount' => 150.00,
            'category' => 'Food',
            'date' => '2024-01-05',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats?start_date=2023-12-01&end_date=2024-01-31');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        $this->assertEquals(250.00, $data['total']);
        $this->assertCount(2, $data['byMonth']);
        
        $months = collect($data['byMonth'])->pluck('month')->toArray();
        $this->assertEquals(['2023-12', '2024-01'], $months);
    }

    /**
     * Test dashboard handles special characters in category names
     */
    public function test_dashboard_handles_special_characters_in_categories(): void
    {
        Expense::create([
            'name' => 'Expense',
            'amount' => 100.00,
            'category' => 'Food & Drinks',
            'date' => '2024-01-15',
        ]);

        Expense::create([
            'name' => 'Expense',
            'amount' => 50.00,
            'category' => 'Health/Medical',
            'date' => '2024-01-20',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        $this->assertCount(2, $data['byCategory']);
        
        $categories = collect($data['byCategory'])->pluck('category')->toArray();
        $this->assertContains('Food & Drinks', $categories);
        $this->assertContains('Health/Medical', $categories);
    }

    /**
     * Test dashboard returns consistent response structure
     */
    public function test_dashboard_returns_consistent_structure(): void
    {
        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'byCategory',
                'total',
                'byMonth',
            ]);
    }

    /**
     * Test dashboard with expenses at boundary dates
     */
    public function test_dashboard_includes_boundary_dates(): void
    {
        Expense::create([
            'name' => 'Start Date Expense',
            'amount' => 100.00,
            'category' => 'Food',
            'date' => '2024-01-01',
        ]);

        Expense::create([
            'name' => 'End Date Expense',
            'amount' => 200.00,
            'category' => 'Food',
            'date' => '2024-01-31',
        ]);

        $response = $this->getJson('/api/expenses/dashboard/stats?start_date=2024-01-01&end_date=2024-01-31');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Both expenses should be included (inclusive boundaries)
        $this->assertEquals(300.00, $data['total']);
    }

    /**
     * Test dashboard performance with many expenses
     */
    public function test_dashboard_handles_many_expenses(): void
    {
        // Create 100 expenses across different categories and months
        for ($i = 1; $i <= 100; $i++) {
            Expense::create([
                'name' => "Expense $i",
                'amount' => rand(10, 500) / 10, // Random amount between 1 and 50
                'category' => ['Food', 'Transportation', 'Entertainment', 'Utilities'][$i % 4],
                'date' => '2024-' . str_pad(($i % 12) + 1, 2, '0', STR_PAD_LEFT) . '-15',
            ]);
        }

        $response = $this->getJson('/api/expenses/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Verify aggregations work correctly
        $this->assertGreaterThan(0, $data['total']);
        $this->assertCount(4, $data['byCategory']);
        $this->assertCount(12, $data['byMonth']);
    }
}