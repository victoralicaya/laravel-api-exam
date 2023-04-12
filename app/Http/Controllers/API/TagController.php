<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TagRequest $request)
    {
        $data = $request->validated();
        $task = Task::find($data['task_id']);

        if (is_null($task)) {
            return response()->json(['message' => 'Task Not Found.'], 404);
        }

        if (!is_null($task) && $task->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Task does not belong to you.'], 401);
        }

        foreach ($data['text'] as $value) {
            Tag::create([
                'task_id' => $data['task_id'],
                'text' => $value
            ]);
        }

        $tagData = Tag::where('task_id', $data['task_id'])->get();

        return TagResource::collection($tagData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json([], 204);
    }
}
