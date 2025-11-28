<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => App\Http\Middleware\RoleMiddleware::class,
            'branch' => App\Http\Middleware\BranchMiddleware::class,
        ]);
        $middleware->append(\App\Http\Middleware\IncreaseInputLimits::class);

        // ឬបើចង់ដាក់តែ route hr ទេ (កាន់តែល្អ)
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\IncreaseInputLimits::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
