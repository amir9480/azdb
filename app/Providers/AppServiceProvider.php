<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (['get', 'first', 'firstOrFail', 'count', 'exists'] as $method) {
            Builder::macro($method.'Cached', function ($ttl = 60, ...$parameters) use ($method) {
                /**
                 * @var Builder $this
                 */
                $bindings = $this->getBindings();
                foreach ($bindings as $key => $binding) {
                    if ($binding instanceof \Carbon\Carbon) {
                        $bindings[$key] = $binding->toDateTimeString();
                    }
                }
                return Cache::remember(
                    'eloquent_cache_'.hash(
                        'sha512',
                        $method
                        .$this->getConnection()->getName()
                        .preg_replace('/laravel_reserved_\d+/', 'laravel_reserved', $this->toSql())
                        .json_encode($bindings)
                        .json_encode($this->getEagerLoads())
                        .json_encode($parameters)
                    ),
                    $ttl,
                    function () use ($parameters, $method) {
                        return $this->{$method}(...$parameters);
                    }
                );
            });
        }
    }
}
