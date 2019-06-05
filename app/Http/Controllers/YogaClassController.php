<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\YogaClass;
use App\Helpers\ControllerHelpers;
use Carbon\Carbon;

class YogaClassController extends Controller
{
    public function show(Request $request, $id)
    {
        return ControllerHelpers::showForCurrentUser($request, $id, YogaClass::query());
    }

    public function list(Request $request)
    {
        return ControllerHelpers::listForCurrentUser($request, YogaClass::query());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required|date|unique:yoga_classes',
            'student_ids' => 'array',
            'student_ids.*' => 'int'
        ]);

        $yogaClass = YogaClass::create($validatedData);

        $yogaClass->syncStudentsIfArrayContainsStudentIds($validatedData);

        return $yogaClass;
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'student_ids' => 'array',
            'student_ids.*' => 'int'
        ]);

        $yogaClass = YogaClass::findOrFail($id);

        // Will only validate date uniqueness when provided date is 
        // different from the stored date.

        if ($request->has('date')) {

            $receivedDate = Carbon::create($request->date)->format('Y-m-d');
            $existingDate = Carbon::create($yogaClass->date)->format('Y-m-d');

            if ($receivedDate !== $existingDate) {
                $validatedData = array_merge($validatedData, $request->validate([
                    'date' => 'date|unique:yoga_classes',
                ]));
            }
        }

        $yogaClass->update($validatedData);

        $yogaClass->syncStudentsIfArrayContainsStudentIds($validatedData);

        return $yogaClass;
    }

    public function delete($id)
    {
        $yogaClass = YogaClass::findOrFail($id);

        $yogaClass->delete();

        return $yogaClass;
    }
}
