<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $position = [
            [
                'position_code' => "CEO",
                'position_name' => "CEO"
            ],
            [
                'position_code' => "Business & Product Development Manager",
                'position_name' => "Business & Product Development Manager"
            ],
            [
                'position_code' => "Deputy Manager, HR and Admin",
                'position_name' => "Deputy Manager, HR and Admin"
            ],
            [
                'position_code' => "Chief Teller",
                'position_name' => "Chief Teller"
            ],
            [
                'position_code' => "Teller",
                'position_name' => "Teller"
            ],
            [
                'position_code' => "Credit Officer",
                'position_name' => "Credit Officer"
            ],
            [
                'position_code' => "Branch Manager",
                'position_name' => "Branch Manager"
            ],
            [
                'position_code' => "Cleaner",
                'position_name' => "Cleaner"
            ],
            [
                'position_code' => "Senior Credit Officer",
                'position_name' => "Senior Credit Officer"
            ],
            [
                'position_code' => "Senior Officer, FN&ACC",
                'position_name' => "Senior Officer, FN&ACC"
            ],
            [
                'position_code' => "HRBP Manager",
                'position_name' => "HRBP Manager"
            ],
            [
                'position_code' => "Sales Officer",
                'position_name' => "Sales Officer"
            ],
            [
                'position_code' => "Credit Officer Trainee",
                'position_name' => "Credit Officer Trainee"
            ],
            [
                'position_code' => "Head of IT",
                'position_name' => "Head of IT"
            ],
            [
                'position_code' => "Head of FN&ACC",
                'position_name' => "Head of FN&ACC"
            ],
            [
                'position_code' => "Marketing & Branch Support Officer ",
                'position_name' => "Marketing & Branch Support Officer "
            ],
            [
                'position_code' => "MIS Officer",
                'position_name' => "MIS Officer"
            ],
            [
                'position_code' => "Driver",
                'position_name' => "Driver"
            ],
            [
                'position_code' => "Head of Operations  ",
                'position_name' => "Head of Operations  "
            ],
            [
                'position_code' => "Loan Recovery Officer ",
                'position_name' => "Loan Recovery Officer "
            ],
            [
                'position_code' => "Senior Sale & Marketing Officer ",
                'position_name' => "Senior Sale & Marketing Officer "
            ],
            [
                'position_code' => "Chief Credit Officer ",
                'position_name' => "Chief Credit Officer "
            ],
            [
                'position_code' => "Senior HR & Admin Officer ",
                'position_name' => "Senior HR & Admin Officer "
            ],
            [
                'position_code' => "Credit Manager ",
                'position_name' => "Credit Manager "
            ],
            [
                'position_code' => "Head of Risk & Compliance ",
                'position_name' => "Head of Risk & Compliance "
            ],
            [
                'position_code' => "Head of Internal Audit ",
                'position_name' => "Head of Internal Audit "
            ],
        ];
        Position::insert($position);      
    }
}
