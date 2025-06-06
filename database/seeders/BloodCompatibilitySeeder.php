<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BloodCompatibilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $compatibility = [
            ['O-', 'O-'], ['O-', 'O+'], ['O-', 'A-'], ['O-', 'A+'], ['O-', 'B-'], ['O-', 'B+'], ['O-', 'AB-'], ['O-', 'AB+'],
            ['O+', 'O+'], ['O+', 'A+'], ['O+', 'B+'], ['O+', 'AB+'],
            ['A-', 'A-'], ['A-', 'A+'], ['A-', 'AB-'], ['A-', 'AB+'],
            ['A+', 'A+'], ['A+', 'AB+'],
            ['B-', 'B-'], ['B-', 'B+'], ['B-', 'AB-'], ['B-', 'AB+'],
            ['B+', 'B+'], ['B+', 'AB+'],
            ['AB-', 'AB-'], ['AB-', 'AB+'],
            ['AB+', 'AB+'],
        ];

        $bloodTypes = DB::table('blood_types')->pluck('id', 'type');

        foreach ($compatibility as [$donor, $recipient]) {
            DB::table('blood_compatibility')->insert([
                'donor_blood_type_id' => $bloodTypes[$donor],
                'recipient_blood_type_id' => $bloodTypes[$recipient],
            ]);
        }
    }
}
