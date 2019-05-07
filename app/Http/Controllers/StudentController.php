<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;

class StudentController extends Controller
{
    public function show($id)
    {
        $student = Student::findOrFail($id);

        return $student;
    }

    public function list(Request $request)
    {
        $query = Student::query();

        $filterables = collect(['name', 'email', 'phone_number', 'dni']);

        foreach ($request->query() as $key => $value) {
            if ($filterables->contains($key)) {
                $query->where($key, 'like', "%$value%");
            }
        }

        $students = $query->get();

        return $students;
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
