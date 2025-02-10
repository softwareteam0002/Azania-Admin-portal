<?php

namespace App\Http\Controllers\IB;

use App\Http\Controllers\Controller;
use App\IbInstitution;
use App\IbPsLevels;
use App\IbPsMembers;
use App\IbPsPayers;
use App\IbPsPayments;
use App\IbPsServices;
use Illuminate\Http\Request;

class PaymentsSolutionController extends Controller
{
    public function paymentSolutions__bck($title)
    {

        $institutions = IbInstitution::all();

        switch ($title)
        {
            case "payments":

                $requests = IbPsPayments::all();

                return view('ib.payments_solution.payments_solutions',compact('title','requests'));

                break;
            case "members":

                $requests = IbPsMembers::all();

                return view('ib.payments_solution.payments_solutions',compact('title','requests'));

                break;
            case "payers":

                $requests = IbPsPayers::all();

                return view('ib.payments_solution.payments_solutions',compact('title','requests'));

                break;
            case "services":

                $requests = IbPsServices::all();

                return view('ib.payments_solution.payments_solutions',compact('title','requests'));

                break;
            case "levels":

                $requests = IbPsLevels::all();

                return view('ib.payments_solution.payments_solutions',compact('title','requests'));

                break;
            default:

                break;
        }
    }

    public function institutions()
    {
        $institutions = IbInstitution::where('hasPaySolution','1')->get();
        $i = IbPsLevels::all();

        return view('ib.payments_solution.index',compact('institutions','i'));
    }

    public function paymentSolutions($id){
        $institutions = IbInstitution::where('hasPaySolution','1')->get();
        $institution_name = IbInstitution::where('id',$id)->get()[0]->institute_name;
        $payments = IbPsPayments::where('institute_id',$id)->get();
        $members = IbPsMembers::where('institution_id',$id)->get();
        $payers = IbPsPayers::where('institution_id',$id)->get();
        $paymentservices = IbPsServices::where('institution_id',$id)->get();
        $levels = IbPsLevels::where('institution_id',$id)->get();

        return view('ib.payments_solution.details',compact(
            'id',
            'institutions',
            'institution_name',
            'payments',
            'members',
            'payers',
            'paymentservices',
            'levels'
        ));
        
    }
}
