<?php

namespace Addons\Attendance;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AttendanceServiceProvider extends ServiceProvider {

    public function boot() {

        $enabled = DB::table( 'addon_settings' )
            ->where( 'name', 'attendance' )
            ->value( 'enabled' );

        if ( $enabled ) {
            $this->loadViewsFrom( __DIR__ . '/Views/admin/attendance', 'Attendance' );
            $this->loadMigrationsFrom( __DIR__ . '/Migrations' );
            $this->loadRoutesFrom( __DIR__ . '/routes/admin.php' );

            $this->loadHelpers();
        }

    }

    public function register() {
        $this->app->make( 'Addons\Attendance\Controllers\Admin\EmployeeAttendanceController' );
    }

    public function loadHelpers() {
        $helpers = __DIR__ . '/Helpers/Helpers.php';

        if ( file_exists( $helpers ) ) {
            require_once $helpers;
        }
    }
}