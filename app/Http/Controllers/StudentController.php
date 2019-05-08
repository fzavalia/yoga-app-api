<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Helpers\RequestHelpers;

class StudentController extends Controller
{
    public function show(Request $request, $id)
    {
        return RequestHelpers::show($request, $id, Student::query());
    }

    public function list(Request $request)
    {
        return RequestHelpers::list($request, Student::query());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'email',
            'phone_number' => 'string',
            'dni' => 'int'
        ]);

        $student = Student::create($validatedData);

        return $student;
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'string',
            'email' => 'email',
            'phone_number' => 'string',
            'dni' => 'int'
        ]);

        $student = Student::findOrFail($id);

        $student->update($validatedData);

        return $student;
    }

    public function delete($id)
    {
        $student = Student::findOrFail($id);

        $student->delete();

        return $student;
    }
}
