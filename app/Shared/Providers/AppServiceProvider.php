<?php

namespace App\Shared\Providers;

use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Contracts\ShapefileProcessorInterface;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Contracts\ShapefileRepositoryInterface;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Services\PatrimonioShapefileProcessor;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Services\PatrimonioShapefileRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ShapefileProcessorInterface::class, PatrimonioShapefileProcessor::class);
        $this->app->bind(ShapefileRepositoryInterface::class, PatrimonioShapefileRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
