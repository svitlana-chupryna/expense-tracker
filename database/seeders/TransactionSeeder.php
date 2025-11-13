<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');
        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::pluck('id', 'name');
        }

        $samples = [
            ['Coffee', 4.50, 'Food'],
            ['Groceries', 62.30, 'Food'],
            ['Bus Ticket', 2.75, 'Transport'],
            ['Electric Bill', 85.00, 'Utilities'],
            ['Movie Night', 14.99, 'Entertainment'],
            ['Gym Membership', 35.00, 'Health'],
            ['T-shirt', 19.99, 'Shopping'],
            ['Rent', 1200.00, 'Rent'],
        ];

        $today = Carbon::today();

        // Create ~40 transactions over the last 30 days
        for ($i = 0; $i < 40; $i++) {
            $pick = collect($samples)->random();
            $date = $today->copy()->subDays(rand(0, 29));
            Transaction::create([
                'name' => $pick[0],
                'amount' => $pick[1],
                'category_id' => $categories[$pick[2]] ?? $categories->random(),
                'date' => $date,
            ]);
        }
    }
}


