<?php

namespace App\Enums;

enum TaskStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    public function label(): string
    {
        return match($this) {
            self::New => 'Новая',
            self::InProgress => 'В работе',
            self::Completed => 'Завершена',
        };
    }
}
