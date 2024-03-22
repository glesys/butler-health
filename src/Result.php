<?php

namespace Butler\Health;

use Butler\Health\Enums\ResultState;

class Result
{
    public $value = null;

    private function __construct(public string $message, public ResultState $state)
    {
    }

    public static function ok(string $message): static
    {
        return new static($message, ResultState::OK);
    }

    public static function warning(string $message): static
    {
        return new static($message, ResultState::WARNING);
    }

    public static function critical(string $message): static
    {
        return new static($message, ResultState::CRITICAL);
    }

    public static function unknown(string $message): static
    {
        return new static($message, ResultState::UNKNOWN);
    }

    public function value()
    {
        if (func_num_args() === 1) {
            $this->value = func_get_arg(0);
        }

        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'message' => $this->message,
            'state' => $this->state->value,
            'order' => $this->state->order(),
        ];
    }
}
