<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $holidays = [
            [
                'holiday_name' => "New Year's Day",
                'from_date' => '2026-01-01',
                'to_date' => '2026-01-01',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'Victory Over the Genocide Day',
                'from_date' => '2026-01-07',
                'to_date' => '2026-01-07',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => "International Women’s Day",
                'from_date' => '2026-03-08',
                'to_date' => '2026-03-08',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'Khmer New Year',
                'from_date' => '2026-04-14',
                'to_date' => '2026-04-16',
                'day' => 3,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'International Labor Day & Visakha Bucha Day',
                'from_date' => '2026-05-01',
                'to_date' => '2026-05-01',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'Royal Ploughing Ceremony',
                'from_date' => '2026-05-05',
                'to_date' => '2026-05-05',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'Royal Birthday of His Majesty Preah Bat Samdech Preah Baromneath Norodom Sihamoni',
                'from_date' => '2026-05-14',
                'to_date' => '2026-05-14',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => "Her Majesty Queen Mother Norodom Monineath Sihanouk's Birthday",
                'from_date' => '2026-06-18',
                'to_date' => '2026-06-18',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'Constitution Day',
                'from_date' => '2026-09-24',
                'to_date' => '2026-09-24',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'Royal Pchum Ben Festival',
                'from_date' => '2026-10-10',
                'to_date' => '2026-10-12',
                'day' => 3,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'Commemoration Day of King Father Norodom Sihanouk',
                'from_date' => '2026-10-15',
                'to_date' => '2026-10-15',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'Coronation Day of His Majesty King Norodom Sihamoni',
                'from_date' => '2026-10-29',
                'to_date' => '2026-10-29',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => "Cambodia's Independence Day",
                'from_date' => '2026-11-09',
                'to_date' => '2026-11-09',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'Cambodian Water Festival (Bon Om Touk)',
                'from_date' => '2026-11-23',
                'to_date' => '2026-11-25',
                'day' => 3,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holiday_name' => 'Peace Day',
                'from_date' => '2026-12-29',
                'to_date' => '2026-12-29',
                'day' => 1,
                'remark' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        Holiday::insert($holidays);
    }
}
