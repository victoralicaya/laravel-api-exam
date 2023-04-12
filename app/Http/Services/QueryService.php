<?php

namespace App\Http\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class QueryService
{
    public static function queryFilter(object $params)
    {
        $tasks = Task::query();
        $to = now()->addDay()->format('Y-m-d H:i:s');

        if (!isset($params)) {
            $tasks->orderBy('id', 'DESC');
        }

        if (isset($params->completed_date_from) && !isset($params->completed_date_to)) {
            $tasks->whereBetween('date_completed', [$params->completed_date_from, $to]);
        }

        if (isset($params->completed_date_from) && isset($params->completed_date_to)) {
            $tasks->whereBetween('date_completed', [$params->completed_date_from, $params->completed_date_to]);
        }

        if (isset($params->due_date_from) && !isset($params->due_date_to)) {
            $tasks->whereBetween('due_date', [$params->due_date_from, $to]);
        }

        if (isset($params->due_date_from) && isset($params->due_date_to)) {
            $tasks->whereBetween('due_date', [$params->due_date_from, $params->due_date_to]);
        }

        if (isset($params->archive_date_from) && !isset($params->archive_date_to)) {
            $tasks->whereBetween('archive', [$params->archive_date_from, $to]);
        }

        if (isset($params->archive_date_from) && isset($params->archive_date_to)) {
            $tasks->whereBetween('archive', [$params->archive_date_from, $params->archive_date_to]);
        }

        if (isset($params->title)) {
            $tasks->where('title', 'like', '%' . $params->title . '%');
        }

        if (isset($params->description)) {
            $tasks->where('description', 'like', '%' . $params->description . '%');
        }

        if (isset($params->priority)) {
            $tasks->where('priority', $params->priority);
        }

        if (isset($params->sort)) {
            $tasks->orderBy($params->sort, 'ASC');
        }

        if (isset($params->sort_level)) {
            $tasks->orderBy('priority', $params->sort_level);
        }

        return $tasks->where('user_id', Auth::user()->id)->with('user')->orderBy('id', 'DESC')->paginate(5);
    }
}
