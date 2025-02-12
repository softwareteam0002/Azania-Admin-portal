<?php

namespace App\Http\Controllers;

use App\AuditTrailLogs;


class AuditController extends Controller
{
    public static function index()
    {
        abort_unless(\Gate::allows('um_audit_trail_access'), 403);
        $requests = AuditTrailLogs::orderBy('id', 'DESC')->get();
        return view('audit.index', compact('requests'));
    }
}
