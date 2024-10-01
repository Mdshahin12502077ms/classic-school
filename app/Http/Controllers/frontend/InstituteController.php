<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\CourseModel;
use App\Models\Session;
use App\Models\User;
use App\Models\stRegAvlableAmount;
use Auth;
class InstituteController extends Controller
{
    public function dashboard(){
        $data['instituteStudent']=Student::where('created_by',Auth::user()->id)->count();
        $data['myStudent']=Student::orderBy('id','desc')->with('course','session')->where('created_by',Auth::user()->id)->where('status','pending')->get();
        $institute=Auth::user()->branch_id;
       $data['amount']=stRegAvlableAmount::orderBy('id','desc')->where('institute_id', $institute)->first();
    
        return view('Institute.Dashboard',$data);
    }
}
