<?php

namespace App\Http\Controllers;

use App\Models\Task;
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

    public function insertDataTask(Request $request) {
        if ($request->get('task_name') !== null) {
            $workspaceTask = new Task([
                'taskName' => $request->get('task_name'),
                'taskDurationFrom' => $request->get('task_from_date').' '.$request->get('task_from_time'),
                'taskDurationTo' => $request->get('task_to_date').' '.$request->get('task_to_time'),
                'taskStatus' => $request->get('task_status'),
                'task_userId' => $request->get('userid'),
                'task_workspaceId' => $request->get('workspaceid')
            ]);

            $status = 'Success';
            $message = 'New task for a workspace is inserted!';

            $workspaceTask->save();
        } else {
            $status = 'Failed';
            $message = 'Failed to insert data!';
            $workspaceTask = [];
        }

        return response()->json([$status, $message, $workspaceTask]);
    }

    public function updateDataTask($id, Request $request) {
        // dd($request->all());

        // $findData = Task::findOrFail($id);
        // $findData->taskStatus = $request->get('task_status');
        // $findData->taskCompletionDatetime = $request->get('task_completion_datetime');

        // $findData->save();
        // $status = 'Success';
        // $message = 'Task for a workspace is updated!';

        if ($request->get('task_completion_datetime') !== null) {
            $findData = Task::findOrFail($id);

            $findData->taskStatus = $request->get('task_status');
            $findData->taskCompletionDatetime = $request->get('task_completion_datetime');

            $status = 'Success';
            $message = 'Task for a workspace is updated!';

            $findData->save();
        } else {
            $status = 'Failed';
            $message = 'Failed to update data!';
            $findData = [];
        }

        return response()->json([$status, $message, $findData]);
    }
}
