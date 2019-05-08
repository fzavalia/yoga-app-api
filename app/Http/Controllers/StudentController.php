<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Traits\QueryWhere;
use App\Traits\QueryOrder;
use App\Traits\QueryCover;

class StudentController extends Controller
{
    use QueryWhere, QueryOrder, QueryCover;

    public function show($id)
    {
        $student = Student::findOrFail($id);

        return $student;
    }

    public function list(Request $request)
    {
        $query = Student::query();

        $this->cover($request, $query);

        $this->where($request, $query);

        $this->order($request, $query);

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
