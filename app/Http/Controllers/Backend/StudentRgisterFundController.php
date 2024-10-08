<?php


namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourseModel;
use App\Models\Session;
use App\Models\Student;
use App\Models\EducationYear;
use App\Models\RegistrationSession;
use App\Models\StRegistrationFund;
use App\Models\stRegAvlableAmount;
use App\Models\logoSet;
use App\Models\BackendSettings;


use Nette\Utils\Random;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class StudentRgisterFundController extends Controller
{
    public function add_fund(){

        return view('Backend.admin.Registration.AddFund');
    }

    public function allFund(){

        $data['course']=CourseModel::all();
        $data['session']=Session::with('eduyear')->where('status','Active')->get();
        $data['available_payment']=StRegistrationFund::where('institute_id',Auth::user()->branch_id)->first();


        return view('Backend.admin.Registration.CreateFundView',$data);
    }


    public function getFund(Request $request)
    {
        $course_id = $request->input('course_id');
        $session_id = $request->input('session_id');
        $instituteOwner=Auth::user()->id;

       $getFund=StRegistrationFund::where('session_id',$session_id)->where('course_id',$course_id)->with('session','course')->where('created_by',$instituteOwner)->get();
      $getAmount=stRegAvlableAmount::where('session_id',$session_id)->where('course_id',$course_id)->orderBy('id','desc')->with('StRegistrationFund')->where('created_by',$instituteOwner)->first();
      if($getAmount!=null){
        return response()->json([
          'data'=>$getFund,
          'amount'=>$getAmount->available_amount,
          ]);
      }
     else{
      return response()->json([
        'data'=>$getFund,
        'amount'=>0,
        ]);
     }

    }



    public function fundInsert(Request $request){

        $request->validate([
            'amount' =>'required|numeric',
            'pay_for' =>'required',
            'session_name' =>'required',
            'course_name' =>'required',

        ]);
      $data=new StRegistrationFund();
      $regSession=RegistrationSession::where('session_id',$request->session_id)->orderBy('id','desc')->first();
      $data->institute_id=Auth::user()->branch_id;
      $data->course_id=$request->course_id;
      $data->session_id=$request->session_id;
      $data->reg_session_id= $regSession->id;
      $data->created_by=Auth::user()->id;
      $data->amount=$request->amount;
      $invoice=StRegistrationFund::orderBy('id', 'DESC')->first();

      if( $invoice==null){
        $data->invoice_number='1632'.+1;
      }
      else{
        $last_invoice_number=StRegistrationFund::orderBy('id', 'DESC')->first();
        $data->invoice_number=$last_invoice_number->invoice_number+1;
      }
    //   $av_amount=StRegistrationFund::where('course_id',$request->course_id)->where('session_id',$request->session_id)->orderBy('id', 'DESC')->where('created_by',Auth::user()->id)->first();

    //   if($av_amount==null){
    //     $data->available_amount=$request->amount;
    //   }
    //   else{
    //     $data->available_amount=$av_amount->available_amount+$request->amount;
    //    }

      $data->pay_for=$request->pay_for;
      $data->status='Pending';
      $data->save();
        toastr()->success('Add Fund Successfully Done');
      return redirect()->back();
    }

    public function fundVoucherPdf($id){
            $data['voucher'] = StRegistrationFund::find($id);
            $data['year'] = EducationYear::where('status','Active')->first();
            $data['logo'] = logoSet::first();
            $data['admin'] = BackendSettings::first();

            return view('Backend.admin.Registration.FundVoucherPdf', $data);
            // return $pdf->stream('FundVoucher.pdf');
    }
}
