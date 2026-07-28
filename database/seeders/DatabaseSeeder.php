<?php

namespace Database\Seeders;

use App\Models\Attendance;
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
        $now = Carbon::now();

        // ── Admin ──
        User::create([
            'name' => 'Admin',
            'email' => 'admin@danceacademy.pl',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // ── Teachers ──
        $teacher1 = User::create([
            'first_name' => 'Anna', 'last_name' => 'Kowalska',
            'name' => 'Anna Kowalska',
            'email' => 'anna@danceacademy.pl',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $teacher2 = User::create([
            'first_name' => 'Marek', 'last_name' => 'Nowak',
            'name' => 'Marek Nowak',
            'email' => 'marek@danceacademy.pl',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $teacher3 = User::create([
            'first_name' => 'Ewa', 'last_name' => 'Zielińska',
            'name' => 'Ewa Zielińska',
            'email' => 'ewa@danceacademy.pl',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // ── Students ──
        $studentDefs = [
            ['first_name' => 'Zofia',     'last_name' => 'Wiśniewska',    'email' => 'zofia@student.pl',     'dob' => '2010-03-15', 'pesel' => '10301512345', 'phone' => '500 100 201', 'parent_phone' => '600 100 201', 'tournament' => 'Junior A',   'notes' => null],
            ['first_name' => 'Jan',        'last_name' => 'Zieliński',     'email' => 'jan@student.pl',       'dob' => '2009-07-22', 'pesel' => '09220712345', 'phone' => '500 100 202', 'parent_phone' => '600 100 202', 'tournament' => 'Junior A',   'notes' => null],
            ['first_name' => 'Maria',      'last_name' => 'Lewandowska',   'email' => 'maria@student.pl',     'dob' => '2011-01-10', 'pesel' => '11101012345', 'phone' => '500 100 203', 'parent_phone' => '600 100 203', 'tournament' => 'Junior B',   'notes' => null],
            ['first_name' => 'Piotr',      'last_name' => 'Szymański',     'email' => 'piotr@student.pl',     'dob' => '2008-11-05', 'pesel' => '08110512345', 'phone' => '500 100 204', 'parent_phone' => '600 100 204', 'tournament' => 'Senior A',   'notes' => null],
            ['first_name' => 'Kasia',      'last_name' => 'Wójcik',        'email' => 'kasia@student.pl',     'dob' => '2010-06-18', 'pesel' => '10180612345', 'phone' => '500 100 205', 'parent_phone' => '600 100 205', 'tournament' => 'Junior A',   'notes' => 'Prefers solo performances'],
            ['first_name' => 'Tomasz',     'last_name' => 'Dąbrowski',     'email' => 'tomasz@student.pl',    'dob' => '2009-09-30', 'pesel' => '09300912345', 'phone' => '500 100 206', 'parent_phone' => '600 100 206', 'tournament' => 'Junior A',   'notes' => null],
            ['first_name' => 'Agnieszka',  'last_name' => 'Kozłowska',     'email' => 'agnieszka@student.pl', 'dob' => '2012-04-12', 'pesel' => '12041212345', 'phone' => '500 100 207', 'parent_phone' => '600 100 207', 'tournament' => 'Junior B',   'notes' => null],
            ['first_name' => 'Kamil',      'last_name' => 'Jankowski',     'email' => 'kamil@student.pl',     'dob' => '2007-08-25', 'pesel' => '07250812345', 'phone' => '500 100 208', 'parent_phone' => '600 100 208', 'tournament' => 'Senior B',   'notes' => 'Team captain'],
            ['first_name' => 'Oliwia',     'last_name' => 'Mazur',         'email' => 'oliwia@student.pl',    'dob' => '2011-05-20', 'pesel' => '11052012345', 'phone' => '500 100 209', 'parent_phone' => '600 100 209', 'tournament' => 'Junior B',   'notes' => null],
            ['first_name' => 'Jakub',      'last_name' => 'Krawczyk',      'email' => 'jakub@student.pl',     'dob' => '2008-12-01', 'pesel' => '08120112345', 'phone' => '500 100 210', 'parent_phone' => '600 100 210', 'tournament' => 'Senior A',   'notes' => null],
            ['first_name' => 'Natalia',    'last_name' => 'Pawlak',        'email' => 'natalia@student.pl',   'dob' => '2010-08-14', 'pesel' => '10081412345', 'phone' => '500 100 211', 'parent_phone' => '600 100 211', 'tournament' => 'Junior A',   'notes' => null],
            ['first_name' => 'Filip',      'last_name' => 'Michalski',     'email' => 'filip@student.pl',     'dob' => '2009-02-28', 'pesel' => '09022812345', 'phone' => '500 100 212', 'parent_phone' => '600 100 212', 'tournament' => 'Junior A',   'notes' => null],
            ['first_name' => 'Weronika',   'last_name' => 'Sikora',        'email' => 'weronika@student.pl',  'dob' => '2011-11-03', 'pesel' => '11110312345', 'phone' => '500 100 213', 'parent_phone' => '600 100 213', 'tournament' => 'Junior B',   'notes' => null],
            ['first_name' => 'Bartek',     'last_name' => 'Wozniak',       'email' => 'bartek@student.pl',    'dob' => '2007-04-17', 'pesel' => '07041712345', 'phone' => '500 100 214', 'parent_phone' => '600 100 214', 'tournament' => 'Senior B',   'notes' => null],
            ['first_name' => 'Karolina',   'last_name' => 'Czajka',        'email' => 'karolina@student.pl',  'dob' => '2010-01-09', 'pesel' => '10010912345', 'phone' => '500 100 215', 'parent_phone' => '600 100 215', 'tournament' => 'Junior A',   'notes' => null],
            ['first_name' => 'Dawid',      'last_name' => 'Lis',           'email' => 'dawid@student.pl',     'dob' => '2008-06-26', 'pesel' => '08062612345', 'phone' => '500 100 216', 'parent_phone' => '600 100 216', 'tournament' => 'Senior A',   'notes' => null],
            ['first_name' => 'Maja',       'last_name' => 'Górska',        'email' => 'maja@student.pl',      'dob' => '2012-09-11', 'pesel' => '12091112345', 'phone' => '500 100 217', 'parent_phone' => '600 100 217', 'tournament' => 'Junior B',   'notes' => null],
            ['first_name' => 'Szymon',     'last_name' => 'Chmielewski',   'email' => 'szymon@student.pl',    'dob' => '2009-03-04', 'pesel' => '09030412345', 'phone' => '500 100 218', 'parent_phone' => '600 100 218', 'tournament' => 'Junior A',   'notes' => null],
            ['first_name' => 'Julia',      'last_name' => 'Borkowska',     'email' => 'julia@student.pl',     'dob' => '2011-07-30', 'pesel' => '11073012345', 'phone' => '500 100 219', 'parent_phone' => '600 100 219', 'tournament' => 'Junior B',   'notes' => null],
            ['first_name' => 'Maciej',     'last_name' => 'Kamiński',      'email' => 'maciej@student.pl',    'dob' => '2006-10-15', 'pesel' => '06101512345', 'phone' => '500 100 220', 'parent_phone' => '600 100 220', 'tournament' => 'Senior A',   'notes' => 'Competition team leader'],
            ['first_name' => 'Lena',       'last_name' => 'Błaszczyk',     'email' => 'lena@student.pl',      'dob' => '2012-02-19', 'pesel' => '12021912345', 'phone' => '500 100 221', 'parent_phone' => '600 100 221', 'tournament' => 'Junior B',   'notes' => null],
            ['first_name' => 'Adrian',     'last_name' => 'Pietrzak',      'email' => 'adrian@student.pl',    'dob' => '2008-05-07', 'pesel' => '08050712345', 'phone' => '500 100 222', 'parent_phone' => '600 100 222', 'tournament' => 'Senior B',   'notes' => null],
        ];

        $students = [];
        foreach ($studentDefs as $d) {
            $students[] = User::create([
                'first_name'          => $d['first_name'],
                'last_name'           => $d['last_name'],
                'name'                => $d['first_name'] . ' ' . $d['last_name'],
                'email'               => $d['email'],
                'password'            => Hash::make('password'),
                'role'                => 'student',
                'date_of_birth'       => $d['dob'],
                'pesel'               => $d['pesel'],
                'phone_number'        => $d['phone'],
                'parent_phone_number' => $d['parent_phone'],
                'tournament_group'    => $d['tournament'],
                'notes'               => $d['notes'],
            ]);
        }

        // ── Categories ──
        $ballet      = DanceCategory::create(['name' => 'Ballet',      'description' => 'Classical ballet technique and artistry']);
        $hiphop      = DanceCategory::create(['name' => 'Hip-Hop',      'description' => 'Urban dance styles and street dance']);
        $contemporary = DanceCategory::create(['name' => 'Contemporary', 'description' => 'Modern and contemporary dance']);
        $salsa       = DanceCategory::create(['name' => 'Salsa',       'description' => 'Latin dance styles']);
        $jazz        = DanceCategory::create(['name' => 'Jazz',        'description' => 'Jazz dance technique and choreography']);
        $latin       = DanceCategory::create(['name' => 'Latin',       'description' => 'Latin formation and competitive dance']);
        $breakdance  = DanceCategory::create(['name' => 'Breakdance',  'description' => 'Breaking, power moves and freezes']);
        $kids        = DanceCategory::create(['name' => 'Kids Dance',  'description' => 'Fun introductory dance for young children']);

        // ── Groups ──
        $g1  = DanceGroup::create(['name' => 'Ballet Beginners',       'category_id' => $ballet->id,       'teacher_id' => $teacher1->id, 'schedule' => 'Monday & Wednesday, 16:00-17:30']);
        $g2  = DanceGroup::create(['name' => 'Ballet Advanced',        'category_id' => $ballet->id,       'teacher_id' => $teacher1->id, 'schedule' => 'Tuesday & Thursday, 18:00-19:30']);
        $g3  = DanceGroup::create(['name' => 'Hip-Hop Crew',           'category_id' => $hiphop->id,       'teacher_id' => $teacher2->id, 'schedule' => 'Monday & Friday, 17:00-18:30']);
        $g4  = DanceGroup::create(['name' => 'Contemporary Fusion',    'category_id' => $contemporary->id, 'teacher_id' => $teacher1->id, 'schedule' => 'Wednesday & Saturday, 10:00-11:30']);
        $g5  = DanceGroup::create(['name' => 'Salsa Social',           'category_id' => $salsa->id,        'teacher_id' => $teacher2->id, 'schedule' => 'Friday, 19:00-20:30']);
        $g6  = DanceGroup::create(['name' => 'Jazz Funk',              'category_id' => $jazz->id,         'teacher_id' => $teacher3->id, 'schedule' => 'Tuesday & Thursday, 16:30-18:00']);
        $g7  = DanceGroup::create(['name' => 'Latin Formation',        'category_id' => $latin->id,        'teacher_id' => $teacher2->id, 'schedule' => 'Wednesday & Friday, 18:00-19:30']);
        $g8  = DanceGroup::create(['name' => 'Breakdance Basics',      'category_id' => $breakdance->id,   'teacher_id' => $teacher3->id, 'schedule' => 'Monday & Thursday, 17:30-19:00']);
        $g9  = DanceGroup::create(['name' => 'Kids Dance Party',       'category_id' => $kids->id,         'teacher_id' => $teacher3->id, 'schedule' => 'Saturday, 10:00-11:00']);
        $g10 = DanceGroup::create(['name' => 'Ballet Competition',     'category_id' => $ballet->id,       'teacher_id' => $teacher1->id, 'schedule' => 'Saturday & Sunday, 09:00-11:00']);

        // ── Group assignments ──
        $g1->students()->attach([$students[0]->id, $students[2]->id, $students[4]->id, $students[10]->id]);
        $g2->students()->attach([$students[1]->id, $students[3]->id, $students[9]->id, $students[15]->id]);
        $g3->students()->attach([$students[1]->id, $students[5]->id, $students[6]->id, $students[11]->id, $students[13]->id]);
        $g4->students()->attach([$students[0]->id, $students[7]->id, $students[16]->id]);
        $g5->students()->attach([$students[2]->id, $students[4]->id, $students[5]->id, $students[7]->id, $students[14]->id]);
        $g6->students()->attach([$students[8]->id, $students[10]->id, $students[12]->id, $students[18]->id]);
        $g7->students()->attach([$students[3]->id, $students[9]->id, $students[14]->id, $students[17]->id]);
        $g8->students()->attach([$students[6]->id, $students[11]->id, $students[19]->id, $students[20]->id]);
        $g9->students()->attach([$students[16]->id, $students[20]->id, $students[17]->id]);
        $g10->students()->attach([$students[0]->id, $students[1]->id, $students[3]->id, $students[15]->id, $students[19]->id]);

        // ── Payments ──
        $monthly = fn ($student, $group, $from, $until) =>
            Payment::create([
                'student_id'  => $student->id,
                'dance_group_id' => $group->id,
                'pass_type'   => 'monthly',
                'amount'      => 150.00,
                'valid_from'  => $from,
                'valid_until' => $until,
                'status'      => $until->isFuture() || $until->isToday() ? 'active' : 'expired',
            ]);

        $single = fn ($student, $group, $date) =>
            Payment::create([
                'student_id'  => $student->id,
                'dance_group_id' => $group->id,
                'pass_type'   => 'single',
                'amount'      => 25.00,
                'valid_from'  => $date,
                'valid_until' => $date,
                'status'      => $date->isFuture() || $date->isToday() ? 'active' : 'expired',
            ]);

        // Current month – active
        $monthly($students[0],  $g1,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[1],  $g2,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[2],  $g1,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[3],  $g2,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[4],  $g5,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[5],  $g3,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[6],  $g3,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[7],  $g4,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[8],  $g6,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[9],  $g2,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[10], $g1,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[11], $g3,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[12], $g6,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[13], $g3,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[14], $g5,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[15], $g2,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[16], $g4,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[17], $g7,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[18], $g6,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[19], $g8,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $monthly($students[20], $g8,  $now->copy()->startOfMonth(), $now->copy()->endOfMonth());

        // Expiring soon (within 7 days)
        $monthly($students[0],  $g4,  $now->copy()->subMonth(), $now->copy()->addDays(3));
        $monthly($students[5],  $g5,  $now->copy()->subMonth(), $now->copy()->addDays(5));
        $monthly($students[9],  $g7,  $now->copy()->subMonth(), $now->copy()->addDays(7));
        $monthly($students[11], $g8,  $now->copy()->subMonth(), $now->copy()->addDays(2));

        // Expired – last month
        $monthly($students[0],  $g1,  $now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth());
        $monthly($students[7],  $g4,  $now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth());
        $monthly($students[13], $g3,  $now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth());

        // Single passes – past
        $single($students[2],  $g4, $now->copy()->subDays(14));
        $single($students[4],  $g1, $now->copy()->subDays(10));
        $single($students[6],  $g5, $now->copy()->subDays(7));
        $single($students[14], $g3, $now->copy()->subDays(5));
        $single($students[17], $g7, $now->copy()->subDays(3));

        // Single passes – today / future
        $single($students[1],  $g3, $now->copy());
        $single($students[10], $g6, $now->copy()->addDays(1));
        $single($students[16], $g9, $now->copy()->addDays(2));

        // ── Attendance ──
        $allGroups  = [$g1, $g2, $g3, $g4, $g5, $g6, $g7, $g8];
        $teachers   = [$teacher1, $teacher2, $teacher3, $teacher1, $teacher2, $teacher3, $teacher2, $teacher3];
        $statuses   = ['present', 'present', 'present', 'present', 'absent', 'excused'];

        foreach (range(1, 21) as $dayOffset) {
            $date = $now->copy()->subDays($dayOffset)->toDateString();
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;

            foreach ($allGroups as $i => $group) {
                $groupDays = match ($i) {
                    0 => [1, 3],       // Mon, Wed
                    1 => [2, 4],       // Tue, Thu
                    2 => [1, 5],       // Mon, Fri
                    3 => [3, 6],       // Wed, Sat
                    4 => [5],          // Fri
                    5 => [2, 4],       // Tue, Thu
                    6 => [3, 5],       // Wed, Fri
                    7 => [1, 4],       // Mon, Thu
                };

                if (!in_array($dayOfWeek, $groupDays)) {
                    continue;
                }

                foreach ($group->students as $student) {
                    Attendance::updateOrCreate(
                        [
                            'student_id'     => $student->id,
                            'dance_group_id' => $group->id,
                            'date'           => $date,
                        ],
                        [
                            'status'     => $statuses[array_rand($statuses)],
                            'recorded_by' => $teachers[$i]->id,
                        ]
                    );
                }
            }
        }
    }
}
