<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamRequestController extends Controller
{
    public function index()
    {
        return ExamRequest::with('items.exam', 'items.package')->paginate(10);
    }

    public function show($id)
    {
        return ExamRequest::with('items.exam', 'items.package')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.exam_id' => 'required|exists:exams,id',
            'items.*.laterality' => 'nullable|in:OD,OE,AO',
            'items.*.comment' => 'nullable|string',
            'items.*.group' => 'required|string|in:Individual,Grupo 1,Grupo 2,Grupo 3,Grupo 4,Grupo 5',
            'items.*.package_id' => 'nullable|exists:packages,id'
        ]);

        $requestModel = ExamRequest::create();
        foreach ($data['items'] as $item) {
            $requestModel->items()->create($item);
        }

        return $requestModel->load('items.exam');
    }

    public function print($id)
    {
        $examRequest = ExamRequest::with(['items.exam', 'items.package'])->findOrFail($id);

        // Mock do paciente/médico
        $patient = [
            'name' => 'Jose Silva',
            'cpf' => '123.456.789-10'
        ];

        $doctor = 'Dr. John Doe';

        // Separa itens da requisição por group
        $groups = $examRequest->items->groupBy('group');

        $groupObservations = [];

        foreach ($groups as $group => $items) {
            $groupObservations[$group] = $items->pluck('package')
                ->filter()
                ->pluck('observations')
                ->unique()
                ->implode(' ; ');
        }

        $pdf = Pdf::loadView('pdf.exam-request', [
            'examRequest' => $examRequest,
            'groups' => $groups,
            'groupObservations' => $groupObservations,
            'patient' => $patient,
            'doctor' => $doctor
        ])->setPaper('a4');

        return $pdf->download("solicitacao-exames-{$examRequest->id}.pdf");
    }
}
