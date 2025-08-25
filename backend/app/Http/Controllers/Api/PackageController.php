<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        return Package::with('exams')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'observations' => 'nullable|string',
            'exams' => 'required|array',
            'exams.*' => 'exists:exams,id',
        ]);

        $package = Package::create($data);
        $package->exams()->sync($data['exams']);

        return $package->load('exams');
    }

    public function show(Package $package)
    {
        return $package->load('exams');
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'observations' => 'nullable|string',
            'exams' => 'required|array',
            'exams.*' => 'exists:exams,id',
        ]);

        $package->update($data);
        $package->exams()->sync($data['exams']);

        return $package->load('exams');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return response()->noContent();
    }
}

