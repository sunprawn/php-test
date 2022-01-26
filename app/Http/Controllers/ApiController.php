<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\Console\Input\Input;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $sortBy = $request->input('sort') ? $request->input('sort') : 'enroll_date';

        $enrollments = Enrollment::select('id','status','student_id','course_id', 'enroll_date', 'complete_date')
            ->when($status, function($query, $status) {
                return $query->whereIn('status', explode(',', $status));
            })
            ->orderBy($sortBy, $request->input('desc') ? 'desc' : 'asc' )
            ->with(['students' => function($query) {
                $query->select('id', 'name');
            }])
            ->with(['courses' => function($query) {
                $query->select('id', 'name');
            }])
            ->paginate(20);

        return JsonResource::collection($enrollments);
    }

    public function search(Request $request, $search)
    {
        $status = $request->input('status');
        $sortBy = $request->input('sort') ? $request->input('sort') : 'enroll_date';
        $query = Enrollment::with('students')->with('courses');

        $result = $query->when($status, function($query, $status) {
                    return $query->whereIn('status', explode(',', $status));
                })->where(function ($query) use ($search) {
                    $query->whereHas('students', function($query) use ($search) {
                            $query->where('name', 'LIKE', '%' . $search . '%');
                        })->orWhereHas('courses', function($query) use ($search) {
                            $query->where('name', 'LIKE', '%' . $search . '%');
                        });
                })->orderBy($sortBy, $request->input('desc') ? 'desc' : 'asc' )
                ->paginate(20);

        return JsonResource::collection($result);
    }

    public function student(Request $request, $id)
    {
/*        $student = Student::with([
                        'enrollments' => function ($query) {
                            $query->select('id', 'student_id', 'course_id', 'status', 'enroll_date', 'complete_date');
                        },
                        'enrollments.courses' => function($query) {
                            $query->select('id', 'name');
                        }])
                    ->find($id);*/
        $status = $request->input('status');
        $sortBy = $request->input('sort') ? $request->input('sort') : 'enroll_date';

        $query = Enrollment::with('students')->with('courses');

        $result = $query->when($status, function($query, $status) {
                return $query->whereIn('status', explode(',', $status));
            })->whereHas('students', function($query) use ($id) {
                $query->where('id', $id);
            })->orderBy($sortBy, $request->input('desc') ? 'desc' : 'asc' )->paginate(20);

        return JsonResource::collection($result);
    }

    public function course(Request $request, $id)
    {
        $status = $request->input('status');
        $sortBy = $request->input('sort') ? $request->input('sort') : 'enroll_date';

        $query = Enrollment::with('students')->with('courses');
        $result = $query->when($status, function($query, $status) {
            return $query->whereIn('status', explode(',', $status));
        })->whereHas('courses', function($query) use ($id) {
            $query->where('id', $id);
        })->orderBy($sortBy, $request->input('desc') ? 'desc' : 'asc' )->paginate(20);

        return JsonResource::collection($result);
    }



}
