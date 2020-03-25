<?php

namespace Ecopro\Base\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Ecopro\Base\Events\ExceptionEvent::class => [
            \Ecopro\Base\Listeners\ExceptionListener::class
        ],
        \Ecopro\Base\Events\OutputEvent::class => [
            \Ecopro\Base\Listeners\OutputListener::class
        ],
        \Ecopro\Base\Events\RequestEvent::class => [
            \Ecopro\Base\Listeners\RequestListener::class
        ],
    ];
}
