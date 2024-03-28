<?php

namespace Butler\Health\Enums;

enum ResultState: string
{
    case OK = 'ok';
    case WARNING = 'warning';
    case CRITICAL = 'critical';
    case UNKNOWN = 'unknown';

    public function order(): int
    {
        return match ($this) {
            self::CRITICAL => 3,
            self::WARNING => 2,
            self::OK => 1,
            self::UNKNOWN => 0,
            default => 0,
        };
    }
}
