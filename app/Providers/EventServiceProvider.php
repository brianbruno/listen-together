<?php

namespace App\Providers;

use App\Events\MusicaIniciada;
use App\Jobs\ProcessarFilas;
use App\Listeners\FinalizarMusica;
use App\Listeners\ProximaFila;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MusicaIniciada::class => [
            ProximaFila::class,
            FinalizarMusica::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
