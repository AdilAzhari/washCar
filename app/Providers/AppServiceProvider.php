<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Bay;
use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\Package;
use App\Models\QueueEntry;
use App\Models\User;
use App\Models\Wash;
use App\Observers\QueueEntryObserver;
use App\Observers\WashObserver;
use App\Policies\AppointmentPolicy;
use App\Policies\BayPolicy;
use App\Policies\BranchPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\PackagePolicy;
use App\Policies\QueuePolicy;
use App\Policies\StaffPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Branch::class => BranchPolicy::class,
        Bay::class => BayPolicy::class,
        QueueEntry::class => QueuePolicy::class,
        Appointment::class => AppointmentPolicy::class,
        Package::class => PackagePolicy::class,
        InventoryItem::class => InventoryPolicy::class,
        User::class => StaffPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Register observers
        Wash::observe(WashObserver::class);
        QueueEntry::observe(QueueEntryObserver::class);
    }
}
