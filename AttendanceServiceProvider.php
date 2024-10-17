<?php

namespace Addons\Attendance;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AttendanceServiceProvider extends ServiceProvider {

    public function boot() {

        $enabled = DB::table( 'addon_settings' )
            ->where( 'name', 'attendance' )
            ->value( 'enabled' );

        if ( $enabled ) {
            $this->loadViewsFrom( __DIR__ . '/resources/Views/admin/attendance', 'Attendance' );
            $this->loadMigrationsFrom( __DIR__ . '/Migrations' );
            $this->loadRoutesFrom( __DIR__ . '/routes/admin.php' );

            $this->loadHelpers();
        }

    }

    public function register() {
        $this->app->make( 'Addons\Attendance\Controllers\Admin\EmployeeAttendanceController' );
        $this->app->make( 'Addons\Attendance\Controllers\Admin\AttendanceAddonUpdateController' );
    }

    protected function loadRoutesWithMiddleware() {
        Route::middleware( 'auth' ) // Specify your middleware here
            ->group( __DIR__ . '/routes/admin.php' );
    }

    public function loadHelpers() {
        $helpers = __DIR__ . '/Helpers/Helpers.php';

        if ( file_exists( $helpers ) ) {
            require_once $helpers;
        }
    }
}