<?php

namespace Addons\Attendance;

use Illuminate\Support\ServiceProvider;

class AttendanceServiceProvider extends ServiceProvider {

    public function boot() {

        // $enabled = DB::table( 'addon_settings' )
        //     ->where( 'name', 'attendance' )
        //     ->value( 'enabled' );

        $this->mergeConfigFrom( __DIR__ . '/config/attendance.php', 'attendance' );

        $enabled = config( 'attendance.enabled' );

        if ( $enabled ) {
            $this->loadViewsFrom( __DIR__ . '/resources/Views/admin/attendance', 'Attendance' );
            $this->loadViewsFrom( __DIR__ . '/resources/Views/admin/leave_application', 'LeaveApplication' );
            $this->loadMigrationsFrom( __DIR__ . '/Migrations' );

            $this->loadRoutesFrom( __DIR__ . '/routes/admin.php' );

            $this->loadHelpers();
        }

    }

    public function register() {
        $this->app->make( 'Addons\Attendance\Controllers\Admin\EmployeeAttendanceController' );
        $this->app->make( 'Addons\Attendance\Controllers\Admin\AttendanceAddonUpdateController' );
        $this->app->make( 'Addons\Attendance\Controllers\Admin\LeaveApplicationController' );
    }

    public function loadHelpers() {
        $helpers = __DIR__ . '/Helpers/Helpers.php';

        if ( file_exists( $helpers ) ) {
            require_once $helpers;
        }
    }

    public function getSidebarItems() {
        $enabled = config( 'attendance.enabled' );
        if ( $enabled ) {
            return [
                "Attendance" => [
                    ['name' => 'Attendance', 'url' => route( 'attendance.index' )],
                    ['name' => 'Leave Application', 'url' => route( 'leave_application.index' )],
                ],
            ];
        }
    }

    public function getOrder() {
        return 1; // Define the order for this provider
    }
}