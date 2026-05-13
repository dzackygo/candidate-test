<?php

namespace App\Providers;

use App\Contracts\SupplierExportServiceInterface;
use App\Contracts\SupplierImportServiceInterface;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Policies\CltLayerPolicy;
use App\Policies\CltLayupPolicy;
use App\Policies\SupplierPolicy;
use App\Services\SupplierExportService;
use App\Services\SupplierImportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SupplierExportServiceInterface::class, SupplierExportService::class);
        $this->app->bind(SupplierImportServiceInterface::class, SupplierImportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(CltLayup::class, CltLayupPolicy::class);
        Gate::policy(CltLayer::class, CltLayerPolicy::class);
    }
}
