<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArchiveRequest;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\StatusRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Services\QueryService;
use App\Models\FileUpload;
use App\Models\Tag;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @queryParam completed_date_from Date From.
     * @queryParam completed_date_to Date To.
     * @queryParam due_date_from Due Date From.
     * @queryParam due_date_to Due Date To.
     * @queryParam archive_date_from Archive Date From.
     * @queryParam archive_date_to Archive Date To.
     * @queryParam title string Title.
     * @queryParam description string Description.
     * @queryParam priority int Priority (1 = Low, 2 = Normal, 3 = High, 4 = Urgent)
     * @queryParam sort string Sort By title, description, due_date, created_at, completed_date
     * @queryParam sort_level string Sort By Priority (value = asc or desc)
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $archived_tasks = Task::where('user_id', Auth::user()->id)->whereNotNull('archive')->get();

        $archived_tasks->each(function($task, $key){
            $arcDate = Carbon::parse($task->archive)->addDays(7)->format('Y-m-d H:i:s');
            if ($arcDate == now()->format('Y-m-d H:i:s')) {
                $task = Task::find($task->id);
                $task->delete();
            }
        });

        foreach ($archived_tasks as $archived_task) {
            $arcDate = Carbon::parse($archived_task->archive)->addDays(7)->format('Y-m-d H:i:s');
            if ($arcDate == now()->format('Y-m-d H:i:s')) {
                $task = Task::find($archived_task->id);
                $task->delete();
            }
        }

        $data = QueryService::queryFilter($request);

        return TaskResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        $user_id = Auth::user()->id;
        $data = $request->validated();

        $data['user_id'] = $user_id;

        return new TaskResource(Task::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        if (!is_null($task) && $task->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Task does not belong to you.'], 401);
        }

        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, Task $task)
    {
        if (!is_null($task) && $task->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Task does not belong to you.'], 401);
        }

        $data = $request->validated();

        $task->update($data);

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if (!is_null($task) && $task->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Task does not belong to you.'], 401);
        }

        $task->delete();
        $task->tags->each(function($tag, $key){
            $tag = Tag::find($tag->id);
            $tag->delete();
        });
        $task->files->each(function($file, $key) {
            $file = FileUpload::find($file->id);
            $file->delete();
        });
        File::deleteDirectory(config('filesystems.disks.public.root').'/'.$task->user->id.'/'.$task->id);

        return response()->json([], 204);
    }

    /**
     * Change task status to complete or incomplete.
     * Default: incomplete.
     *
     * @param Request $request
     * @param int $id
     */
    public function changeStatus(Task $task)
    {
        if (!is_null($task) && $task->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Task does not belong to you.'], 401);
        }

        if ($task->status == "incomplete") {
            $task->update([
                'status' => 'completed',
                'date_completed' => now(),
            ]);
        } else {
            $task->update([
                'status' => 'incomplete',
                'date_completed' => null,
            ]);
        }

        return new TaskResource($task);
    }

    /**
     * Archive a task.
     *
     * @param Request $request
     * @param int $id
     */
    public function archiveTask(Task $task)
    {
        if (!is_null($task) && $task->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Task does not belong to you.'], 401);
        }

        if (is_null($task->archive)) {
            $task->update([
                'archive' => now(),
            ]);
        } else {
            $task->update([
                'archive' => null,
            ]);
        }

        return new TaskResource($task);
    }
}
