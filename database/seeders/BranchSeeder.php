<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                "branch_code" => "MBO",
                "Branch_name" => "Head Office",
                "location"    => "PP",
            ],
            [
                "branch_code" => "TKO",
                "branch_name" => "Takeo Office",
                "location"    => "TK",
            ]
        ];

        Branch::insert($branches);
    }
}
