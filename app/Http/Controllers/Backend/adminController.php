<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\stRegAvlableAmount;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class adminController extends Controller
{
    public function dashboard(){
        $studentCount=Student::all()->count();
        $studentRegCount=Student::where('status','registered')->count();
        $earning=stRegAvlableAmount::orderBy('id','desc')->first();
        
        $totalEarn = $earning ? $earning->total_earn : 0;

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
    
        // Query for current month's income
        $monthlyIncome = stRegAvlableAmount::select(
                DB::raw('SUM(amount) as total_income')
            )
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->first();
    
        // Query for current year's income
        $yearlyIncome = stRegAvlableAmount::select(
                DB::raw('SUM(amount) as total_income')
            )
            ->whereYear('date', $currentYear)
            ->first();
          
        return view('Backend.admin.include.index',compact('studentCount','monthlyIncome','yearlyIncome','studentRegCount'));
    }

 
}
