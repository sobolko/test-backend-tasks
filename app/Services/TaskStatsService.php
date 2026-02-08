<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class TaskStatsService
{
    public function get(User $user, ?int $userId = null): array
    {
        $cacheKey = $this->cacheKey($user, $userId);

        return Cache::remember($cacheKey, 60, function () use ($user, $userId) {
            $query = $this->baseQuery($user, $userId);

            return [
                'total' => (clone $query)->count(),
                'by_status' => $query
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
            ];
        });
    }

    private function baseQuery(User $user, ?int $userId)
    {
        $query = Task::query();

        if ($user->hasRole('admin')) {
            if ($userId) {
                $query->whereUserId($userId);
            }
        } else {
            $query->whereUserId($user->id);
        }

        return $query;
    }

    private function cacheKey(User $user, ?int $userId): string
    {
        if ($user->hasRole('admin')) {
            return 'admin_stats_' . ($userId ?? 'all');
        }

        return 'user_stats_' . $user->id;
    }
}
