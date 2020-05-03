<?php

namespace CarlosVini\Serve2;

use Illuminate\Support\ServiceProvider;

/**
 * @internal
 *
 * @final
 */
class Serve2ServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boots application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Serve2Command::class,
            ]);
        }
    }
}
