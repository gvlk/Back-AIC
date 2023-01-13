<?php

namespace App\Modules;

class Response
{
    public function __construct(public int $statusCode, public array $message)
    {
    }

    public static function success(array $message): self
    {
        return new self(200, $message);
    }

    public static function error(string $message): self
    {
        return new self(500, ['error' => $message]);
    }
}
