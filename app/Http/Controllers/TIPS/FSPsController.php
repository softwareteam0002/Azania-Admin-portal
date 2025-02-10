<?php

namespace App\Http\Controllers\TIPS;

use App\Http\Controllers\Controller;
use App\Models\Tips\FSP;
use App\Models\Tips\Institution;
use Illuminate\Http\Request;

class FSPsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fsps = FSP::orderBy('id', 'DESC')->paginate(7);
        return view('tips.fsps.index', compact('fsps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tips.fsps.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $check_fsp = FSP::where('fspId', $request->fspId)->first();
        if($check_fsp)
        {
            return back()->with('error', 'FSP Already Exists');
        }
        else
        {
            $fsp = new FSP();
            $fsp->name = $request->name;
            $fsp->swift_code = $request->swift_code;
            $fsp->fspId = $request->fspId;
            $fsp->fsp_bin = $request->fsp_bin;
            $fsp->fsp_type = $request->fsp_type;
            $fsp->status = 0;
            $fsp->save();
            return back()->with('success', 'FSP added successfully!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {

        $check_type = FSP::where('id', $id)
		->where('fsp_type', '=', 'MNO')->first();
		
		if($check_type){
			//status 1 is inactive 
			 FSP::where('id', $id)->update((['status'=>'1']));
			 return back()->with('success', 'FSP is deactivated successfully!');
		}
    }
	
	 public function deactivate($id)
    {

        $check_type = FSP::where('id', $id)
		->where('fsp_type', '=', 'MNO')->first();
		
		if($check_type){
			//status 0 is active 
			 FSP::where('id', $id)->update((['status'=>'']));
			 return back()->with('success', 'FSP is activated successfully!');
		}
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(FSP $fsp)
    {
        return view('tips.fsps.edit', compact('fsp'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FSP $fsp)
    {
        if($fsp)
        {
            $fsp->name = $request->name;
            $fsp->swift_code = $request->swift_code;
            $fsp->fspId = $request->fspId;
            $fsp->fsp_bin = $request->fsp_bin;
            $fsp->fsp_type = $request->fsp_type;
            $fsp->save();
           // return redirect('/tips/fsps')->with('success', 'FSP updated successfully!');
			return redirect()->route('tips.fsps.index')->with('success', 'FSP updated successfully!');
        }
        else
        {
			
            return redirect()->route('tips.fsps.index')->with('error', 'FSP update failed!');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(FSP $fsp)
    {
        if($fsp)
        {
            $fsp->delete();
            return back()->with('success', 'FSP Deleted Successfully');
        }
        else
        {
            return back()->with('error', 'FSP Delete Failed');
        }

    }
    public function getFSP()
    {
        $source_fsp = Institution::where('status', 0)->where('is_source', 1)->get(['fspId','name']);
        //$source_fsp = Institution::where('status', 0)->where('is_source', 1)->get(['name','swift_code','fsp_bin', 'fspId', 'fsp_type']);
        //$fsps_banks = FSP::where('status', 0)->where('fsp_type', 'BANK')->get(['name','swift_code','fsp_bin', 'fspId', 'fsp_type']);
        $fsps_banks = FSP::where('status', 0)->where('fsp_type', 'BANK')->get(['fspId', 'name','fsp_type']);
        $fsps_mnos = FSP::where('status', 0)->where('fsp_type', 'MNO')->get(['name','swift_code','fsp_bin', 'fspId', 'fsp_type']);
		$fsps_wallets = FSP::where('status', 0)->where('fsp_type', 'WALLET')->get(['name','swift_code','fsp_bin', 'fspId', 'fsp_type']);
        if(!empty($fsps_banks) || !empty($fsps_mnos) || !empty($fsps_wallets) || !empty($source_fsp))
        {
            $responseCode = "200";
            $responseMessage = "Success";
        }
        else
        {
            $responseCode = "100";
            $responseMessage = "No data";
        }

        return json_encode(array('responseCode'=>$responseCode,'responseMessage'=>$responseMessage,'source_fsp'=>$source_fsp,'fsps_banks'=>$fsps_banks,'fsps_mnos'=>$fsps_mnos,'fsps_wallets'=>$fsps_wallets));
    }
}
