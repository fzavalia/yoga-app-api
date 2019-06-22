<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\YogaClass;
use App\Helpers\ControllerHelpers;

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
            'student_ids' => 'array',
            'student_ids.*' => 'int'
        ]);

        $validatedData['user_id'] = $request->user()->id;

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

        ControllerHelpers::validateUserCanHandleResource($request, $yogaClass);

        $yogaClass->update($validatedData);

        $yogaClass->syncStudentsIfArrayContainsStudentIds($validatedData);

        return $yogaClass;
    }

    public function delete(Request $request, $id)
    {
        $yogaClass = YogaClass::findOrFail($id);

        ControllerHelpers::validateUserCanHandleResource($request, $yogaClass);

        $yogaClass->delete();

        return $yogaClass;
    }
}
