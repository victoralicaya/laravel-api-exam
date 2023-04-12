<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Http\Resources\FileUploadResource;
use App\Models\FileUpload;
use App\Models\Task;
use GuzzleHttp\Promise\Each;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class FileUploadController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileUploadRequest $request)
    {
        $task = Task::find($request->task_id);

        if (is_null($task)) {
            return response()->json(['message' => 'Task Not Found.'], 404);
        }

        if (!is_null($task) && $task->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Task does not belong to you.'], 401);
        }

        $user_id = Auth::user()->id;
        $data = $request->validated();
        $files = $request->file('files');

        if ($request->hasFile('files')) {
            foreach ($files as $file) {
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileExt = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $file_name = $fileName.'-'.now()->format('his').'.'.$fileExt;
                $path = $user_id.'/'.$data['task_id'];
                $file->storeAs($path, $file_name, 'public');

                FileUpload::create([
                    'task_id' => $data['task_id'],
                    'name' => $file_name,
                    'path' => $path.'/'.$file_name
                ]);
            }
        }

        $fileData = FileUpload::where('task_id', $data['task_id'])->get();

        return FileUploadResource::collection($fileData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(FileUpload $fileUpload)
    {
        $fileUpload->delete();
        File::delete(config('filesystems.disks.public.root').'/'.$fileUpload->task->user->id.'/'.$fileUpload->task->id.'/'.$fileUpload->name);

        return response()->json([], 204);
    }
}
