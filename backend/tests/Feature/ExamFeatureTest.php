<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Package;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_an_exam()
    {
        $payload = [
            'name' => 'Exame de Vista',
            'laterality' => 'OD',
        ];

        $response = $this->postJson('/api/exams', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Exame de Vista']);

        $this->assertDatabaseHas('exams', ['name' => 'Exame de Vista']);
    }

    public function test_it_can_create_a_package_with_exams()
    {
        $exams = Exam::factory()->count(2)->create();

        $payload = [
            'name' => 'Pacote Oftalmológico',
            'observations' => 'Jejum de 12h',
            'exams' => $exams->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/packages', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Pacote Oftalmológico']);

        $this->assertDatabaseCount('exam_package', 2);
    }

    public function test_it_can_create_an_exam_request_with_items()
    {
        $exam1 = Exam::factory()->create();
        $exam2 = Exam::factory()->create();
        $package = Package::factory()->hasAttached([$exam2])->create();

        $payload = [
            'items' => [
                [
                    'exam_id' => $exam1->id,
                    'group' => 'Grupo 1',
                    'comment' => 'Exame individual',
                    'laterality' => 'OD',
                    'package_id' => null,
                ],
                [
                    'exam_id' => $exam2->id,
                    'group' => 'Grupo 2',
                    'comment' => 'Exame de pacote',
                    'laterality' => 'OE',
                    'package_id' => $package->id,
                ],
            ]
        ];

        $response = $this->postJson('/api/exam-requests', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'items' => [['exam' => ['id', 'name']]]]);

        $this->assertDatabaseCount('exam_request_items', 2);
    }

    public function test_it_can_list_exam_requests()
    {
        ExamRequest::factory()
            ->count(10)
            ->hasItems(2)
            ->create();

        $response = $this->getJson('/api/exam-requests');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'items' => []]
                 ]);
    }

    public function test_it_validates_exam_creation_without_name()
    {
        $response = $this->postJson('/api/exams', [
            'laterality' => 'OD'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    public function test_it_validates_exam_creation_with_invalid_laterality()
    {
        $response = $this->postJson('/api/exams', [
            'name' => 'Exame inválido',
            'laterality' => 'XYZ'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['laterality']);
    }

    public function test_it_validates_package_creation_without_exams()
    {
        $response = $this->postJson('/api/packages', [
            'name' => 'Pacote Inválido',
            'exams' => []
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['exams']);
    }

    public function test_it_validates_package_creation_with_nonexistent_exam()
    {
        $response = $this->postJson('/api/packages', [
            'name' => 'Pacote com exame inexistente',
            'exams' => [999]
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['exams.0']);
    }

    public function test_it_validates_exam_request_creation_without_items()
    {
        $response = $this->postJson('/api/exam-requests', [
            'items' => []
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['items']);
    }

    public function test_it_validates_exam_request_with_invalid_exam_id()
    {
        $response = $this->postJson('/api/exam-requests', [
            'items' => [
                [
                    'exam_id' => 999,
                    'group' => 'Grupo 1',
                    'comment' => 'Erro',
                    'laterality' => 'OD'
                ]
            ]
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['items.0.exam_id']);
    }

    public function test_it_validates_exam_request_with_invalid_group()
    {
        $exam = Exam::factory()->create();

        $response = $this->postJson('/api/exam-requests', [
            'items' => [
                [
                    'exam_id' => $exam->id,
                    'group' => 'Grupo XYZ',
                    'comment' => 'Erro',
                    'laterality' => 'OD'
                ]
            ]
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['items.0.group']);
    }

    public function test_it_can_generate_pdf_for_single_group()
    {
        $exams = Exam::factory()->count(5)->create();
        
        $package = Package::factory()->create();
        $package->exams()->sync($exams->slice(3, 2)->pluck('id'));

        $examRequest = ExamRequest::create();

        foreach ($exams->take(3) as $exam) {
            $examRequest->items()->create([
                'exam_id' => $exam->id,
                'laterality' => 'AO',
                'comment' => 'Avulso',
                'group' => 'Grupo 1',
                'package_id' => null,
            ]);
        }

        foreach ($package->exams as $exam) {
            $examRequest->items()->create([
                'exam_id' => $exam->id,
                'laterality' => 'AO',
                'comment' => 'Do pacote',
                'group' => 'Grupo 1',
                'package_id' => $package->id,
            ]);
        }

        $response = $this->get("/api/exam-requests/{$examRequest->id}/print");

        $response->assertStatus(200);
        
        $path = storage_path('app/testing');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents("{$path}/exam_request_single_group.pdf", $response->getContent());
    }

    public function test_it_can_generate_pdf_for_multiple_groups()
    {
        $exams = Exam::factory()->count(20)->create();
        $packages = Package::factory(6)->create();

        foreach ($packages as $index => $package) {
            $package->exams()->sync($exams->slice($index * 2, 2)->pluck('id'));
        }

        $groups = ['Individual','Grupo 1', 'Grupo 2', 'Grupo 3', 'Grupo 4','Grupo 5'];
        $examRequest = ExamRequest::create();


        foreach ($exams->take(6) as $index => $exam) {
            $group = $groups[$index % count($groups)];
            $examRequest->items()->create([
                'exam_id' => $exam->id,
                'laterality' => ['AO', 'OD', 'OE'][$index % 3],
                'comment' => "Avulso {$index}",
                'group' => $group,
                'package_id' => null,
            ]);
        }

        foreach ($packages as $index => $package) {
            $group = $groups[$index % count($groups)];
            foreach ($package->exams as $exam) {
                $examRequest->items()->create([
                    'exam_id' => $exam->id,
                    'laterality' => 'AO',
                    'comment' => 'Do pacote',
                    'group' => $group,
                    'package_id' => $package->id,
                ]);
            }
        }

        $response = $this->get("/api/exam-requests/{$examRequest->id}/print");

        $response->assertStatus(200);

        $path = storage_path('app/testing');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents("{$path}/exam_request_multi_group.pdf", $response->getContent());
    }
}
