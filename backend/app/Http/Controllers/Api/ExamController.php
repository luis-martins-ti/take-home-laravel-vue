<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        return Exam::create($request->validate([
            'name' => 'required|string',
            'laterality' => 'nullable|in:OD,OE,AO',
        ]));
    }

    public function show(Exam $exam)
    {
        return $exam;
    }

    public function update(Request $request, Exam $exam)
    {
        $exam->update($request->validate([
            'name' => 'required|string',
            'laterality' => 'nullable|in:OD,OE,AO',
        ]));

        return $exam;
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return response()->noContent();
    }
}
