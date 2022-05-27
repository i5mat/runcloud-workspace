<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkspaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getWorkspaceList()
    {
        $workspace = DB::table('workspaces')->where('workspace_userId', '=', Auth::id())->get();

        return response()->json($workspace);
    }

    public function displayHome()
    {
        return view('workspace.index');
    }

    public function insertData(Request $request) {
        if ($request->get('workspace_name') !== null) {
            $workspace = new Workspace([
                'workspaceName' => $request->get('workspace_name'),
                'workspaceStatus' => $request->get('workspace_status'),
                'workspace_userId' => Auth::id(),
            ]);

            $status = 'Success';
            $message = 'New workspace is inserted!';

            $workspace->save();
        } else {
            $status = 'Failed';
            $message = 'Failed to insert data!';
            $workspace = [];
        }

        return response()->json([$status, $message, $workspace]);
    }
}
