<?php

use Addons\Attendance\Controllers\Admin\AttendanceAddonUpdateController;
use Addons\Attendance\Controllers\Admin\EmployeeAttendanceController;
use Addons\Attendance\Controllers\Admin\EmployeeOvertimeController;
use Addons\Attendance\Controllers\Admin\LeaveApplicationController;
use Addons\Attendance\Controllers\Admin\LeaveEncashmentController;
use Illuminate\Support\Facades\Route;

Route::get( '/auth-check', function () {
    return auth()->check() ? 'Authenticated' : 'Not Authenticated';
} );

Route::middleware( ['web', 'auth'] )->group( function () {

    Route::prefix( 'admin' )->group( function () {

        // ------ Employee attendance -----
        Route::prefix( 'attendance' )->group( function () {
            Route::get( '/', [EmployeeAttendanceController::class, 'index'] )->name( 'attendance.index' );
            Route::get( '/create', [EmployeeAttendanceController::class, 'create'] )->name( 'attendance.create' );
            Route::post( '/store', [EmployeeAttendanceController::class, 'store'] )->name( 'attendance.store' );
            Route::get( '/edit/{id}', [EmployeeAttendanceController::class, 'edit'] )->name( 'attendance.edit' );
            Route::post( '/update/{id}', [EmployeeAttendanceController::class, 'update'] )->name( 'attendance.update' );
            Route::delete( '/delete/{id}', [EmployeeAttendanceController::class, 'destroy'] )->name( 'attendance.delete' );
        } );

        //---My Attendance

        Route::prefix( 'my-attendance' )->group( function () {
            Route::get( '/', [EmployeeAttendanceController::class, 'myAttendance'] )->name( 'my.attendance' );
            Route::post( '/check-in-check-out', [EmployeeAttendanceController::class, 'checkInCheckOut'] )->name( 'attendance.check-in-check-out' );
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

        Route::get( 'my-leave-application/create', [LeaveApplicationController::class, 'myLeaveApplication'] )->name( 'leave_application.create' );

        // ------ Leave Encashment -----
        Route::prefix( 'leave-encashment' )->group( function () {
            Route::get( '/', [LeaveEncashmentController::class, 'index'] )->name( 'leave.encashment.index' );
            Route::post( '/store', [LeaveEncashmentController::class, 'store'] )->name( 'leave.encashment.store' );
            Route::get( '/create', [LeaveEncashmentController::class, 'create'] )->name( 'leave.encashment.create' );
            Route::get( '/edit/{id}', [LeaveEncashmentController::class, 'edit'] )->name( 'leave.encashment.edit' );
            Route::post( '/update/{id}', [LeaveEncashmentController::class, 'update'] )->name( 'leave.encashment.update' );
            Route::delete( '/delete/{id}', [LeaveEncashmentController::class, 'destroy'] )->name( 'leave.encashment.destroy' );
            Route::delete( 'all/delete', [LeaveEncashmentController::class, 'deleteAll'] )->name( 'leave.encashment.delete.all' );
        } );

        // ------ Employee overtime -----
        Route::prefix( 'employee-overtime' )->group( function () {
            Route::get( '/', [EmployeeOvertimeController::class, 'index'] )->name( 'employee.overtime.index' );
            Route::post( '/store', [EmployeeOvertimeController::class, 'store'] )->name( 'employee.overtime.store' );
            Route::get( '/edit/{id}', [EmployeeOvertimeController::class, 'edit'] )->name( 'employee.overtime.edit' );
            Route::post( '/update', [EmployeeOvertimeController::class, 'update'] )->name( 'employee.overtime.update' );
            Route::delete( '/delete/{id}', [EmployeeOvertimeController::class, 'destroy'] )->name( 'employee.overtime.destroy' );
            Route::post( 'all/delete', [EmployeeOvertimeController::class, 'deleteSelected'] )->name( 'employee.overtime.deleteSelected' );
            Route::get( '/filter', [EmployeeOvertimeController::class, 'filter'] )->name( 'employee.overtime.filter' );
        } );

        //----Addons Route
        Route::get( 'attendance/addon/update', [AttendanceAddonUpdateController::class, 'downloadAndUpdate'] )->name( 'attendance.addon.update' );

    } );

} );