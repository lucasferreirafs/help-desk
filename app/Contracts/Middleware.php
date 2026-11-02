<?php
declare(strict_types=1);

namespace App\Contracts;

interface Middleware
{
    public function handle(callable $next): void;
}