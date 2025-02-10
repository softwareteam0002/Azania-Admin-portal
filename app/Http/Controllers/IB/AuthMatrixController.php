<?php


namespace App\Http\Controllers\IB;

use App\AuditLogs;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Middleware\AuthGates;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use DB;
use App\IbTransferType;
use App\IbAccount;
use Illuminate\Support\Carbon;
use App\Models\AuthMatrix\MatrixRoleService;
use App\Models\AuthMatrix\MatrixRole;
use App\Models\AuthMatrix\MatrixRoleServiceSignatory;
use App\Models\AuthMatrix\TransactionApproveLog;

use Auth;
//use Illuminate\Validation\ValidationException;


class AuthMatrixController extends Controller
{
    //
    public function setup($id){
        $account = IbAccount::find($id);
        $account_service_roles = IbAccount::find($id)->matrixRoleService()->orderBy('created_at', 'desc')->get();
        $institution = $account->institutions()->with("users")->first();
        $institution_users = $institution->users()->where('action_id', '<>', 1)->get();

        $matrix_roles = MatrixRole::orderBy('created_at', 'desc')->get();
        $services = IbTransferType::all();

        //return response()->json(['account_service_roles' => $account_service_roles], 200);
        return view('ib.AuthMatrix.account_matrix_role',compact('account_service_roles', 'services', 'matrix_roles', 'account', 'institution_users'));
    }

    public function storeAccountServiceRole(Request $request){
        
        $request->validate([
            'matrix_role_id'=>'required',
            'account_number'=>'required',
            'account_id'=>'required',
            'service_id'=>'required',
            "user_id"    => "required|array|min:1",
            'user_id.*'=> 'required|numeric',
        ]);
        
        // validate 
        $this->validateMatrixServiceRole($request);
        //dd($request->all());

        DB::beginTransaction();
        try
        {
            $id = MatrixRoleService::insertGetId([
                'matrix_role_id'=>$request->matrix_role_id,
                'service_id' => $request->service_id,
                'account_number'=> $request->account_number,
                'account_id'=> $request->account_id,
                'created_by'=>Auth::user()->id,
                'created_at' => date("Y-m-d H:i:s")
            ]);

            $int_level = 1;

            foreach($request->user_id as $user)
            {

                MatrixRoleServiceSignatory::insert([
                'matrix_role_service_id' => $id,
                'int_level' => $int_level,
                'user_id' => $user,
                'created_at' => date("Y-m-d H:i:s")
                
                ]);

                $int_level  = $int_level  + 1;
            }

            DB::commit();

            $notification="Matrix Service Role added successfully";
            $color="success";
            //$log = new Helper();
            //return $log->auditTrail("Matrix Service Role","IB",$notification, 'ib/accounts/auth_matrix/setup/'.$request->account_id ,Auth::user()->getAuthIdentifier());
            return redirect('ib/accounts/auth_matrix/setup/'.$request->account_id )->with('notification',$notification)->with('color',$color);
        }catch(\Exception $e){
			//dd($e);
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();

            $notification="Matrix role was not  added!";
            $color="danger";

            return redirect('ib/accounts/auth_matrix/setup/'.$request->account_id )->with('notification',$notification)->with('color',$color);
        }
    }

    public function disableAccountServiceRole($id)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $matrix_role = MatrixRoleService::where('id', $id)->first();

        if($matrix_role->deleted_at == NULL)
           {
            $matrix_role->deleted_at = $now;
            $notification="Matrix role disabled successfully!";
            $color="success";
           }
           else{
            $matrix_role->deleted_at = NULL;
            $notification="Matrix role enabled successfully!";
            $color="success";
           } 
        
        $matrix_role->save();
         

            return redirect()->back()->with('notification',$notification)->with('color',$color);
    }

    public function viewMatrixRole()
    {
        $matrixRoles = MatrixRole::orderBy('id', 'DESC')->get();

        return view('ib.settings.AuthMatrix.matrix_role',compact('matrixRoles'));
    }

    public function storeMatrixRole(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'min_amount'=> 'nullable|numeric',
            'max_amount'=> 'nullable|numeric',
        ]);

        $db_action = MatrixRole::insert([
            'name'=>$request->name,
            'is_sequencial'=> $request->is_sequencial ?? 0,
            'is_range'=> $request->is_range ?? 0,
            'is_any_of'=> $request->is_any_of ?? 0,
            'signatories'=> $request->signatories ?? null,
            'min_amount'=> $request->min_amount ?? null,
            'created_by'=>Auth::user()->id,
            'max_amount'=> $request->max_amount ?? null,
            'created_at' => date("Y-m-d H:i:s")
        ]);
        

        if($db_action==true)
        {
            $notification="Matrix role added successfully";
            $color="success";
            $log = new Helper();
            //return $log->auditTrail("Matrix Role ","IB",$notification,'ib/view_matrix_role',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Matrix role was not  added!";
            $color="danger";
        }

        return redirect('ib/view_matrix_role')->with('notification',$notification)->with('color',$color);
    }

    public function storeMatrixUpdate(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'name'=>'required',
            'min_amount'=> 'nullable|numeric',
            'max_amount'=> 'nullable|numeric',
            'id' => 'required'
        ]);

        //dd($request->all());
        $db_action = MatrixRole::where('id',$request->id)->update([
            'name'=>$request->name,
            'is_sequencial'=> $request->is_sequencial ?? 0,
            'is_range'=> $request->is_range ?? 0,
            'is_any_of'=> $request->is_any_of ?? 0,
            'signatories'=> $request->signatories ?? null,
            'min_amount'=> $request->min_amount ?? null,
            'max_amount'=> $request->max_amount ?? null,
        ]);
        

        if($db_action==true)
        {
            $notification="Matrix role updated successfully";
            $color="success";
            //$log = new Helper();
            //return $log->auditTrail("Matrix Role ","IB",$notification,'ib/view_matrix_role',Auth::user()->getAuthIdentifier());
        }
        else{
            $notification="Matrix role was not  updated!";
            $color="danger";
        }

        return redirect('ib/view_matrix_role')->with('notification',$notification)->with('color',$color);
    
    }

    public function storeMatrixDelete(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        // check if matrix role used

        $db_action = MatrixRoleService::where('matrix_role_id',$request->id)->first();

        if($db_action)
        {
            throw ValidationException::withMessages(['id' => 'Cant Delete Role already used by service!']);
        }else
        {
            $db_action1 = MatrixRole::where('id',$request->id)->delete();
        }
        

        if($db_action1 == true)
        {
            $notification="Matrix role deleted successfully";
            $color="success";
            //$log = new Helper();
            //return $log->auditTrail("Matrix Role ","IB",$notification,'ib/view_matrix_role',Auth::user()->getAuthIdentifier());
        }else{
            $notification="Matrix role was not  deleted!";
            $color="danger";
        }

        return redirect('ib/view_matrix_role')->with('notification',$notification)->with('color',$color);
    
    }

    private function validateMatrixServiceRole($request)
    {
        // validations if for all exists
        $duplicate_role_check = MatrixRoleService::where('matrix_role_id', $request->matrix_role_id)
                                                    ->where('service_id',  $request->service_id)
                                                    ->where('account_number', $request->account_number)
                                                    ->whereNull('deleted_at')
                                                    ->first();
        if($duplicate_role_check)
        {
            throw ValidationException::withMessages(['service_id' => 'Service role Exists!']);
        }

        // check if none range exists
        $check_against_none_range = DB::connection('sqlsrv2')->table('tbl_matrix_role_service')
                                ->select('tbl_matrix_roles.*' )
                                ->join('tbl_matrix_roles', 'tbl_matrix_roles.id', '=', 'tbl_matrix_role_service.matrix_role_id')
                                ->where('tbl_matrix_role_service.service_id', $request->service_id)
                                ->where('tbl_matrix_role_service.account_number', $request->account_number)
                                ->whereNull('tbl_matrix_role_service.deleted_at')
                                ->where('tbl_matrix_roles.is_range','<>', 1)
                                ->first();

        if($check_against_none_range)
        {
            // atleast one none range exists
            throw ValidationException::withMessages(['is_range' => 'None Range Already Exists For This Service!']);
        }
        
        $matrix_role = MatrixRole::find($request->matrix_role_id);
        
        if($matrix_role->is_range != 1)
        {
            $check_against_range = DB::connection('sqlsrv2')->table('tbl_matrix_role_service')
                                ->select('tbl_matrix_roles.*' )
                                ->join('tbl_matrix_roles', 'tbl_matrix_roles.id', '=', 'tbl_matrix_role_service.matrix_role_id')
                                ->where('tbl_matrix_role_service.service_id', $request->service_id)
                                ->where('tbl_matrix_role_service.account_number', $request->account_number)
                                ->whereNull('tbl_matrix_role_service.deleted_at')
                                ->where('tbl_matrix_roles.is_range','=', 1)
                                ->first();
            if($check_against_range) 
            {
                // atleast one range with none range
                throw ValidationException::withMessages(['is_range' => 'Unable to add none range role, atleast one range found!']);
            }
        }

        $get_matrix_role_service = MatrixRoleService::where('service_id',  $request->service_id)
                                                        ->where('account_number', $request->account_number)
                                                        ->get();

        if($matrix_role->is_range == 1 && sizeof($get_matrix_role_service) > 0)
        {
            // validate range overlap
            $check_against_min = DB::connection('sqlsrv2')->table('tbl_matrix_role_service')
                                ->select('tbl_matrix_roles.*' )
                                ->join('tbl_matrix_roles', 'tbl_matrix_roles.id', '=', 'tbl_matrix_role_service.matrix_role_id')
                                ->where('tbl_matrix_role_service.service_id', $request->service_id)
                                ->where('tbl_matrix_role_service.account_number', $request->account_number)
                                ->whereNull('tbl_matrix_role_service.deleted_at')
                                ->whereRaw('? between tbl_matrix_roles.min_amount and tbl_matrix_roles.max_amount', array($matrix_role->min_amount))
                                ->first();

            if($check_against_min)
            {
                // overlap with min_amount exists
                throw ValidationException::withMessages(['service_id' => 'Overlapping Range On the Service In Min Amount Identified!']);
                 
            }

            $check_against_max = DB::connection('sqlsrv2')->table('tbl_matrix_role_service')
                                ->select('tbl_matrix_roles.*' )
                                ->join('tbl_matrix_roles', 'tbl_matrix_roles.id', '=', 'tbl_matrix_role_service.matrix_role_id')
                                ->where('tbl_matrix_role_service.service_id', $request->service_id)
                                ->where('tbl_matrix_role_service.account_number', $request->account_number)
                                ->whereNull('tbl_matrix_role_service.deleted_at')
                                ->whereRaw('? between tbl_matrix_roles.min_amount and tbl_matrix_roles.max_amount', array($matrix_role->max_amount))
                                ->first();

            if($check_against_max)
            {
                // overlap with max_amount exists
                throw ValidationException::withMessages(['service_id' => 'Overlapping Range On the Service In Max Amount Identified!']);
                                     
            }
        }
    }

    public function transactionsApproveLog()
    {
        $transactionApproveLogs = TransactionApproveLog::selectRaw('transactionable_id,transactionable_type, count(transactionable_id) as signatories')
                                                ->orderBy('transactionable_id', 'DESC')
                                                ->orderBy('transactionable_type', 'DESC')
                                                ->groupBy('transactionable_id', 'transactionable_type')
                                                ->get();

        return view('ib.AuthMatrix.transactions',compact('transactionApproveLogs'));
        //return response()->json(['transactions' => $transactionApproveLogs], 200);
    }

    public function viewTransactionApproveLog(Request $request)
    {
        $request->validate([
            'transactionable_id'=>'required',
            'transactionable_type'=>'required'
        ]);

        $transactionApproveLogs = TransactionApproveLog::where('transactionable_id', $request->transactionable_id)
                                                        ->where('transactionable_type', $request->transactionable_type)
                                                        ->get();

        $matrixRoleService = $transactionApproveLogs[0]->matrixRoleService()->first();
        $matrixRole = $transactionApproveLogs[0]->matrixRole()->first();


        //$account_service_role = $transactionApproveLogs->se
        //to note later my_turn 3 previous rejected, my_turn 2 already workonit, 0 waiting for my turn, 1 currently my turn
        
        return view('ib.AuthMatrix.view_transaction_approval_log',compact('matrixRole', 'matrixRoleService', 'transactionApproveLogs'));
                                                        
    }
}

