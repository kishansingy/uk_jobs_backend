<?php

namespace App\Providers;

use App\Repositories\Contracts\JobRepositoryInterface;
use App\Repositories\EloquentJobRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // DIP: Controllers depend on the interface, not the concrete Eloquent implementation.
        // Swap to ElasticsearchJobRepository here when ready — zero controller changes needed.
        $this->app->bind(JobRepositoryInterface::class, EloquentJobRepository::class);
    }
}
