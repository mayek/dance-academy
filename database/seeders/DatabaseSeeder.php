<?php

namespace Database\Seeders;

use App\Models\DanceCategory;
use App\Models\DanceGroup;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@danceacademy.pl',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $teacher1 = User::create([
            'name' => 'Anna Kowalska',
            'email' => 'anna@danceacademy.pl',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $teacher2 = User::create([
            'name' => 'Marek Nowak',
            'email' => 'marek@danceacademy.pl',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $students = [];
        $studentData = [
            ['first_name' => 'Zofia', 'last_name' => 'Wiśniewska', 'email' => 'zofia@student.pl', 'date_of_birth' => '2010-03-15', 'pesel' => '10301512345', 'phone_number' => '500 100 201', 'parent_phone_number' => '600 100 201', 'tournament_group' => 'Junior A'],
            ['first_name' => 'Jan', 'last_name' => 'Zieliński', 'email' => 'jan@student.pl', 'date_of_birth' => '2009-07-22', 'pesel' => '09220712345', 'phone_number' => '500 100 202', 'parent_phone_number' => '600 100 202', 'tournament_group' => 'Junior A'],
            ['first_name' => 'Maria', 'last_name' => 'Lewandowska', 'email' => 'maria@student.pl', 'date_of_birth' => '2011-01-10', 'pesel' => '11101012345', 'phone_number' => '500 100 203', 'parent_phone_number' => '600 100 203', 'tournament_group' => 'Junior B'],
            ['first_name' => 'Piotr', 'last_name' => 'Szymański', 'email' => 'piotr@student.pl', 'date_of_birth' => '2008-11-05', 'pesel' => '08110512345', 'phone_number' => '500 100 204', 'parent_phone_number' => '600 100 204', 'tournament_group' => 'Senior A'],
            ['first_name' => 'Kasia', 'last_name' => 'Wójcik', 'email' => 'kasia@student.pl', 'date_of_birth' => '2010-06-18', 'pesel' => '10180612345', 'phone_number' => '500 100 205', 'parent_phone_number' => '600 100 205', 'tournament_group' => 'Junior A', 'notes' => 'Prefers solo performances'],
            ['first_name' => 'Tomasz', 'last_name' => 'Dąbrowski', 'email' => 'tomasz@student.pl', 'date_of_birth' => '2009-09-30', 'pesel' => '09300912345', 'phone_number' => '500 100 206', 'parent_phone_number' => '600 100 206', 'tournament_group' => 'Junior A'],
            ['first_name' => 'Agnieszka', 'last_name' => 'Kozłowska', 'email' => 'agnieszka@student.pl', 'date_of_birth' => '2012-04-12', 'pesel' => '12041212345', 'phone_number' => '500 100 207', 'parent_phone_number' => '600 100 207', 'tournament_group' => 'Junior B'],
            ['first_name' => 'Kamil', 'last_name' => 'Jankowski', 'email' => 'kamil@student.pl', 'date_of_birth' => '2007-08-25', 'pesel' => '07250812345', 'phone_number' => '500 100 208', 'parent_phone_number' => '600 100 208', 'tournament_group' => 'Senior B', 'notes' => 'Team captain'],
        ];

        foreach ($studentData as $data) {
            $students[] = User::create([
                ...$data,
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);
        }

        $ballet = DanceCategory::create(['name' => 'Ballet', 'description' => 'Classical ballet technique and artistry']);
        $hiphop = DanceCategory::create(['name' => 'Hip-Hop', 'description' => 'Urban dance styles and street dance']);
        $contemporary = DanceCategory::create(['name' => 'Contemporary', 'description' => 'Modern and contemporary dance']);
        $salsa = DanceCategory::create(['name' => 'Salsa', 'description' => 'Latin dance styles']);

        $group1 = DanceGroup::create(['name' => 'Ballet Beginners', 'category_id' => $ballet->id, 'teacher_id' => $teacher1->id, 'schedule' => 'Monday & Wednesday, 16:00-17:30']);
        $group2 = DanceGroup::create(['name' => 'Ballet Advanced', 'category_id' => $ballet->id, 'teacher_id' => $teacher1->id, 'schedule' => 'Tuesday & Thursday, 18:00-19:30']);
        $group3 = DanceGroup::create(['name' => 'Hip-Hop Crew', 'category_id' => $hiphop->id, 'teacher_id' => $teacher2->id, 'schedule' => 'Monday & Friday, 17:00-18:30']);
        $group4 = DanceGroup::create(['name' => 'Contemporary Fusion', 'category_id' => $contemporary->id, 'teacher_id' => $teacher1->id, 'schedule' => 'Wednesday & Saturday, 10:00-11:30']);
        $group5 = DanceGroup::create(['name' => 'Salsa Social', 'category_id' => $salsa->id, 'teacher_id' => $teacher2->id, 'schedule' => 'Friday, 19:00-20:30']);

        $group1->students()->attach([$students[0]->id, $students[2]->id, $students[4]->id]);
        $group2->students()->attach([$students[1]->id, $students[3]->id]);
        $group3->students()->attach([$students[1]->id, $students[5]->id, $students[6]->id]);
        $group4->students()->attach([$students[0]->id, $students[7]->id]);
        $group5->students()->attach([$students[2]->id, $students[4]->id, $students[5]->id, $students[7]->id]);

        $now = Carbon::now();

        Payment::create(['student_id' => $students[0]->id, 'dance_group_id' => $group1->id, 'pass_type' => 'monthly', 'amount' => 150.00, 'valid_from' => $now->copy()->startOfMonth(), 'valid_until' => $now->copy()->endOfMonth(), 'status' => 'active']);
        Payment::create(['student_id' => $students[0]->id, 'dance_group_id' => $group4->id, 'pass_type' => 'single', 'amount' => 25.00, 'valid_from' => $now->copy()->subDays(2), 'valid_until' => $now->copy()->subDays(2), 'status' => 'expired']);
        Payment::create(['student_id' => $students[1]->id, 'dance_group_id' => $group2->id, 'pass_type' => 'monthly', 'amount' => 150.00, 'valid_from' => $now->copy()->startOfMonth(), 'valid_until' => $now->copy()->endOfMonth(), 'status' => 'active']);
        Payment::create(['student_id' => $students[1]->id, 'dance_group_id' => $group3->id, 'pass_type' => 'single', 'amount' => 25.00, 'valid_from' => $now, 'valid_until' => $now, 'status' => 'active']);
        Payment::create(['student_id' => $students[2]->id, 'dance_group_id' => $group1->id, 'pass_type' => 'monthly', 'amount' => 150.00, 'valid_from' => $now->copy()->startOfMonth(), 'valid_until' => $now->copy()->endOfMonth(), 'status' => 'active']);
        Payment::create(['student_id' => $students[3]->id, 'dance_group_id' => $group2->id, 'pass_type' => 'monthly', 'amount' => 150.00, 'valid_from' => $now->copy()->startOfMonth(), 'valid_until' => $now->copy()->endOfMonth(), 'status' => 'active']);
        Payment::create(['student_id' => $students[4]->id, 'dance_group_id' => $group5->id, 'pass_type' => 'monthly', 'amount' => 150.00, 'valid_from' => $now->copy()->startOfMonth(), 'valid_until' => $now->copy()->endOfMonth(), 'status' => 'active']);
        Payment::create(['student_id' => $students[5]->id, 'dance_group_id' => $group3->id, 'pass_type' => 'single', 'amount' => 25.00, 'valid_from' => $now->copy()->subWeek(), 'valid_until' => $now->copy()->subWeek(), 'status' => 'expired']);
        Payment::create(['student_id' => $students[7]->id, 'dance_group_id' => $group4->id, 'pass_type' => 'monthly', 'amount' => 150.00, 'valid_from' => $now->copy()->startOfMonth(), 'valid_until' => $now->copy()->endOfMonth(), 'status' => 'active']);
    }
}
