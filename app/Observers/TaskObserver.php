<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskObserver
{
    public function created(Task $task)
    {
        static::flushTaskCache($task);
    }

    public function updated(Task $task)
    {
        static::flushTaskCache($task);
    }

    public function deleted(Task $task)
    {
        static::flushTaskCache($task);
    }

    protected static function flushTaskCache(Task $task)
    {
        // Сбросить кэш пользователя
        Cache::forget('user_stats_' . $task->user_id);
        // Сбросить кэш для админа (все и по пользователю)
        Cache::forget('admin_stats_all');
        Cache::forget('admin_stats_' . $task->user_id);
        // Можно добавить другие ключи, если используются
    }
}
