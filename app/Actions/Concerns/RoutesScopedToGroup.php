<?php

namespace App\Actions\Concerns;

use App\Http\Middleware\SetGroupFromDomainMiddleware;
use App\Http\Middleware\ShareNextMeetupMiddleware;

trait RoutesScopedToGroup
{
    public function getControllerMiddleware(): array
    {
        return [SetGroupFromDomainMiddleware::class, ShareNextMeetupMiddleware::class];
    }
}
