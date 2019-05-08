<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\YogaClass;
use App\Helpers\ControllerHelpers;

class YogaClassController extends Controller
{
    public function show(Request $request, $id)
    {
        return ControllerHelpers::show($request, $id, YogaClass::query());
    }

    public function list(Request $request)
    {
        return ControllerHelpers::list($request, YogaClass::query());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required|date|unique:yoga_classes',
            'student_ids' => 'array',
            'student_ids.*' => 'int'
        ]);

        $yogaClass = YogaClass::create($validatedData);

        $this->trySyncStudents($validatedData, $yogaClass);

        return $yogaClass;
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'date' => 'date|unique',
            'student_ids' => 'array',
            'student_ids.*' => 'int'
        ]);

        $yogaClass = YogaClass::findOrFail($id);

        $yogaClass->update($validatedData);

        $this->trySyncStudents($validatedData, $yogaClass);

        return $yogaClass;
    }

    public function delete($id)
    {
        $yogaClass = YogaClass::findOrFail($id);

        $yogaClass->delete();

        return $yogaClass;
    }

    private function trySyncStudents(array $arrayWithStudentIds, YogaClass $yogaClass)
    {
        if (array_key_exists('student_ids', $arrayWithStudentIds)) {
            $yogaClass->students()->sync($arrayWithStudentIds['student_ids']);
        }
    }
}
