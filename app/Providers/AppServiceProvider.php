<?php

namespace App\Providers;

use App\Models\Reservation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.tms');
        Paginator::defaultSimpleView('vendor.pagination.tms');

        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $role = strtolower((string) $user?->role);
            $isReservationReviewer = in_array($role, ['admin', 'owner', 'manager'], true);

            $pendingReservationCount = $isReservationReviewer
                ? Reservation::where('status', 'Pending')
                    ->whereDate('service_date', '>=', now('Asia/Makassar')->toDateString())
                    ->count()
                : 0;

            $view->with([
                'isReservationReviewer' => $isReservationReviewer,
                'pendingReservationCount' => $pendingReservationCount,
            ]);
        });
    }
}
