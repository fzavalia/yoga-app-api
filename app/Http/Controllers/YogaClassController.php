<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\YogaClass;
use App\Helpers\ControllerHelpers;
use App\Rules\UniqueWith;

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
        $userId = $request->user()->id;

        $validatedData = $request->validate([
            'date' => ['required', 'date', new UniqueWith('yoga_classes', 'date', UniqueWith::makeOtherColumn('user_id', $userId))],
            'student_ids' => 'array',
            'student_ids.*' => 'int'
        ]);

        $validatedData['user_id'] = $userId;

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

        // Will only validate date uniqueness when provided date is 
        // different from the stored date.

        if ($request->has('date')) {

            $receivedDate = Carbon::create($request->date)->format('Y-m-d');
            $existingDate = Carbon::create($yogaClass->date)->format('Y-m-d');

            if ($receivedDate !== $existingDate) {
                $validatedData = array_merge($validatedData, $request->validate([
                    'date' => ['date', new UniqueWith('yoga_classes', 'date', UniqueWith::makeOtherColumn('user_id', $request->user()->id))],
                ]));
            }
        }

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
