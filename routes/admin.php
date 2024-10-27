<?php

use Addons\Attendance\Controllers\Admin\AttendanceAddonUpdateController;
use Addons\Attendance\Controllers\Admin\EmployeeAttendanceController;
use Addons\Attendance\Controllers\Admin\LeaveApplicationController;
use Illuminate\Support\Facades\Route;

Route::get( '/auth-check', function () {
    return auth()->check() ? 'Authenticated' : 'Not Authenticated';
} );

Route::middleware( ['web', 'auth'] )->group( function () {

    Route::prefix( 'admin' )->group( function () {

        // ------ Employee attendance -----
        Route::prefix( 'employee-attendance' )->group( function () {
            Route::get( '/', [EmployeeAttendanceController::class, 'index'] )->name( 'attendance.index' );
            Route::get( '/create', [EmployeeAttendanceController::class, 'create'] )->name( 'attendance.create' );
            Route::post( '/store', [EmployeeAttendanceController::class, 'store'] )->name( 'attendance.store' );
            Route::get( '/edit/{id}', [EmployeeAttendanceController::class, 'edit'] )->name( 'attendance.edit' );
            Route::post( '/update/{id}', [EmployeeAttendanceController::class, 'update'] )->name( 'attendance.update' );
            Route::delete( '/delete/{id}', [EmployeeAttendanceController::class, 'destroy'] )->name( 'attendance.delete' );
        } );

        // ------ Leave Application -----

        Route::prefix( 'leave-application' )->group( function () {
            Route::get( '/', [LeaveApplicationController::class, 'index'] )->name( 'leave_application.index' );
            Route::get( 'create', [LeaveApplicationController::class, 'create'] )->name( 'leave_application.create' );
            Route::post( 'store', [LeaveApplicationController::class, 'store'] )->name( 'leave_application.store' );
            Route::get( 'edit/{id}', [LeaveApplicationController::class, 'edit'] )->name( 'leave_application.edit' );
            Route::post( 'update/{id}', [LeaveApplicationController::class, 'update'] )->name( 'leave_application.update' );
            Route::delete( 'delete/{id}', [LeaveApplicationController::class, 'destroy'] )->name( 'leave_application.delete' );
        } );

        //----Addons Route
        Route::get( 'attendance/addon/update', [AttendanceAddonUpdateController::class, 'downloadAndUpdate'] )->name( 'attendance.addon.update' );

    } );

} );
