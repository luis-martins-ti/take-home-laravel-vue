<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Package;
use App\Models\ExamRequest;

class ExamDemoSeeder extends Seeder
{
    public function run(): void
    {
        $exams = Exam::factory()->count(33)->create();
        $packages = Package::factory()->count(11)->create();

        foreach ($packages as $index => $package) {
            $package->exams()->sync(
                $exams->slice($index * 3, 3)->pluck('id')
            );
        }

        $groups = ['Individual','Grupo 1','Grupo 2','Grupo 3','Grupo 4','Grupo 5'];

        for ($i = 0; $i < 11; $i++) {
            $examRequest = ExamRequest::create();

            foreach ($exams->slice($i * 2, 2) as $index => $exam) {
                $examRequest->items()->create([
                    'exam_id' => $exam->id,
                    'laterality' => ['AO','OD','OE'][$index % 3],
                    'comment' => "Avulso {$i}-{$index}",
                    'group' => $groups[($i + $index) % count($groups)],
                    'package_id' => null,
                ]);
            }

            if (isset($packages[$i])) {
                foreach ($packages[$i]->exams as $exam) {
                    $examRequest->items()->create([
                        'exam_id' => $exam->id,
                        'laterality' => 'AO',
                        'comment' => "Do pacote {$i}",
                        'group' => $groups[$i % count($groups)],
                        'package_id' => $packages[$i]->id,
                    ]);
                }
            }
        }
    }
}
