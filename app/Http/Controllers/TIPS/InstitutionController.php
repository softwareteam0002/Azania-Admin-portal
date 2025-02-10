<?php

namespace App\Http\Controllers\TIPS;

use App\Http\Controllers\Controller;
use App\Models\Tips\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $institutions = Institution::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('tips.institutions.index', compact('institutions'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tips.institutions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $check_institution = Institution::where('fspId', $request->fspId)->first();
        if($check_institution)
        {
            return back()->with('error', 'Institution Already Exists');
        }
        else
        {
            $institution = new Institution();
            $institution->name = $request->name;
            $institution->swift_code = $request->swift_code;
            $institution->fspId = $request->fspId;
            $institution->fsp_bin = $request->fsp_bin;
            $institution->fsp_type = $request->fsp_type;
            $institution->primary_color = $request->primary_color;
            $institution->secondary_color = $request->secondary_color;
            $institution->is_source = $request->is_source;
            $institution->status = 0;
            $institution->save();
            return back()->with('success', 'Institution added successfully!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Institution $institution)
    {
        return view('tips.institutions.edit', compact('institution'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Institution $institution)
    {
        if($institution)
        {
            $institution->name = $request->name;
            $institution->swift_code = $request->swift_code;
            $institution->fspId = $request->fspId;
            $institution->fsp_bin = $request->fsp_bin;
            $institution->fsp_type = $request->fsp_type;
            $institution->primary_color = $request->primary_color;
            $institution->secondary_color = $request->secondary_color;
            $institution->is_source = $request->is_source;
            $institution->save();
            return back()->with('success', 'Institution updated successfully!');
        }
        else
        {
            return back()->with('error', 'Institution update failed!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
