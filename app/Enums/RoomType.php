<?php

namespace App\Enums;

enum RoomType: string
{
    case CLASSROOM = 'classroom';
    case LAB = 'lab';
    case OFFICE = 'office';
    case LIBRARY = 'library';
    case GYMNASIUM = 'gymnasium';
    case STORAGE = 'storage';
    case STAFFROOM = 'staffroom';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
