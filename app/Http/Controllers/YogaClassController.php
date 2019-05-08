<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\YogaClass;
use App\Traits\QueryOrder;
use App\Traits\QueryCover;
use App\Traits\QueryWhere;

class YogaClassController extends Controller
{
    use QueryWhere, QueryOrder, QueryCover;

    public function show(Request $request, $id)
    {
        $query = YogaClass::query();

        $this->cover($request, $query);

        $yogaClass = $query->findOrFail($id);

        return $yogaClass;
    }

    public function list(Request $request)
    {
        $query = YogaClass::query();

        $this->cover($request, $query);

        $this->where($request, $query);
        
        $this->order($request, $query);

        $yogaClasses = $query->get();

        return $yogaClasses;
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
