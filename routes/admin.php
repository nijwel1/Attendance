<?php

use Addons\Attendance\Controllers\Admin\AttendanceAddonUpdateController;
use Addons\Attendance\Controllers\Admin\EmployeeAttendanceController;
use Illuminate\Support\Facades\Route;

Route::get( '/test-auth', function () {
    return auth()->check() ? 'Authenticated' : 'Not Authenticated';
} );

Route::middleware( ['web'] )->group( function () {

    Route::prefix( 'admin' )->group( function () {

        // ------ Employee attendance -----
        Route::prefix( 'employee-attendance' )->group( function () {
            Route::get( '/', [EmployeeAttendanceController::class, 'index'] )->name( 'attendance.index' );
            Route::get( '/create', [EmployeeAttendanceController::class, 'create'] )->name( 'attendance.create' );
            Route::post( '/store', [EmployeeAttendanceController::class, 'store'] )->name( 'attendance.store' );
            Route::get( '/edit/{id}', [EmployeeAttendanceController::class, 'edit'] )->name( 'attendance.edit' );
            Route::post( '/update/{id}', [EmployeeAttendanceController::class, 'update'] )->name( 'attendance.update' );
            Route::delete( '/delete/{id}', [EmployeeAttendanceController::class, 'destroy'] )->name( 'attendance.deletre' );
        } );

        //----Addons Route
        Route::get( 'attendance/addon/update', [AttendanceAddonUpdateController::class, 'downloadAndUpdate'] )->name( 'attendance.addon.update' );

    } );

} );
