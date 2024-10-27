<?php

namespace Addons\Attendance\Controllers\Admin;

use Addons\Attendance\Models\LeaveApplication;
use Addons\Employee\Models\Department;
use Addons\Employee\Models\Employee;
use Addons\Employee\Models\LeaveType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LeaveApplicationController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request ) {
        try {
            DB::beginTransaction();

            $year     = $request->input( 'year' ) ?? date( 'Y' );
            $dateFrom = $request->input( 'date_from' ) ?? date( 'd-m-Y' );
            // Adjust the query to filter by year for d-m-Y format
            $leaveApplicationsQuery = LeaveApplication::with( 'employee', 'leaveType' )
                ->where( DB::raw( "STR_TO_DATE(date_from, '%d-%m-%Y')" ), '>=', $year . '-01-01' )
                ->where( DB::raw( "STR_TO_DATE(date_from, '%d-%m-%Y')" ), '<=', $year . '-12-31' )->latest();
            $employeeId = $request->input( 'employee_id' ) ?? null;
            $employee   = Employee::find( $employeeId );

            $departments = Department::with( 'employees' )->get();
            $leaveTypes  = LeaveType::all();
            $users       = User::all();

            if ( $employeeId ) {
                $leaveApplications = $leaveApplicationsQuery->where( 'employee_id', $employeeId )->get();
                $view              = 'LeaveApplication::individual';
            } else {
                $leaveApplications = $leaveApplicationsQuery->get();
                $view              = 'LeaveApplication::index';
            }

            if ( !view()->exists( $view ) ) {
                return view( 'errors.404' );
            }

            return view( $view, compact( 'leaveApplications', 'departments', 'employee', 'year', 'leaveTypes', 'users', 'dateFrom' ) );

            DB::commit();

        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'Failed to fetch leave applications', ['error' => $e->getMessage()] );
            return redirect()->back()->with( 'error', 'Oh! Something went wrong!' );
        }
    }

    public function create( Request $request ) {

        // dd( $request->all() );
        $employee    = Employee::with( 'leaveTable', 'leaveApplication' )->find( $request->id );
        $departments = Department::withWhereHas( 'employees' )->get();
        $leaveTypes  = LeaveType::all();
        $users       = User::get( ['id', 'name', 'type'] );
        $dateFrom    = datepicker_format_reverse( $request->date );
        $view        = 'LeaveApplication::create_form';
        if ( !view()->exists( $view ) ) {
            return view( 'errors.404' );
        } else {
            return view( 'LeaveApplication::create_form', compact( 'employee', 'departments', 'leaveTypes', 'users', 'dateFrom' ) );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( Request $request ) {
        $request->validate( [
            'employee_id'    => 'required',
            'date_from'      => 'required',
            'date_to'        => 'required',
            'leave_type_id'  => 'required',
            'month_to_pay'   => 'required',
            'number_of_days' => 'required | numeric',
            'status'         => 'required',
        ] );
        try {
            DB::beginTransaction();

            $check = LeaveApplication::where( 'employee_id', $request->employee_id )->where( 'date_from', $request->date_from )->exists();

            if ( $check == true ) {
                return redirect()->back()->with( 'warning', 'This user has already submitted a leave application for the date ' . $request->date_from . '. Please select a different date.' );
            }

            $data                 = new LeaveApplication();
            $data->employee_id    = $request->employee_id;
            $data->auth_id        = Auth::user()->id;
            $data->leave_type_id  = $request->leave_type_id;
            $data->leave_table_id = $request->leave_table_id;
            $data->month_to_pay   = $request->month_to_pay;
            $data->date_from      = $request->date_from;
            $data->date_to        = $request->date_to;
            $data->number_of_days = $request->number_of_days;
            $data->remarks        = $request->remarks;
            $data->status         = $request->status;

            if ( $request->attachment ) {
                $data->attachment = fileUpload( $request->attachment, 'backend/assets/files/employee/leave', '' );
            }

            if ( $request->attachment_two ) {
                $data->attachment_two = fileUpload( $request->attachment_two, 'backend/assets/files/employee/leave', '' );
            }

            $data->save();

            DB::commit();
            return redirect()->back()->with( 'success', 'Employee leave application created successfully.' );
        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'Failed to update user details', ['error' => $e->getMessage()] );
            return redirect()->back()->with( 'error', 'Oh ! Something went wrong !' );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( string $id ) {
        try {

            DB::beginTransaction();

            // Your logic here

            DB::commit();

        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'Failed to update user details', ['error' => $e->getMessage()] );
            return redirect()->back()->with( 'error', 'Oh ! Something went wrong !' );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( string $id ) {
        try {

            DB::beginTransaction();

            $leaveApplication = LeaveApplication::with( 'employee' )->find( $id );
            $leaveTypes       = LeaveType::all();
            $departments      = Department::with( 'employees' )->get();
            $users            = User::get( ['id', 'name', 'type'] );

            $view = 'LeaveApplication::edit';
            if ( !view()->exists( $view ) ) {
                return view( 'errors.404' );
            } else {
                return view( 'LeaveApplication::edit', compact( 'departments', 'leaveApplication', 'leaveTypes', 'users' ) );
            }

            DB::commit();

        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'Failed to update user details', ['error' => $e->getMessage()] );
            return redirect()->back()->with( 'error', 'Oh ! Something went wrong !' );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, string $id ) {

        $request->validate( [
            'employee_id'    => 'required',
            'date_from'      => 'required',
            'date_to'        => 'required',
            'leave_type_id'  => 'required',
            'month_to_pay'   => 'required',
            'number_of_days' => 'required | numeric',
            'status'         => 'required',
        ] );

        try {
            DB::beginTransaction();

            $data                 = LeaveApplication::find( $id );
            $data->employee_id    = $request->employee_id;
            $data->auth_id        = Auth::user()->id;
            $data->leave_type_id  = $request->leave_type_id;
            $data->leave_table_id = $request->leave_table_id;
            $data->month_to_pay   = $request->month_to_pay;
            $data->date_from      = $request->date_from;
            $data->date_to        = $request->date_to;
            $data->number_of_days = $request->number_of_days;
            $data->remarks        = $request->remarks;
            $data->status         = $request->status;

            if ( $request->attachment ) {

                if ( file_exists( $data->attachment ) ) {
                    File::delete( $data->attachment );
                }
                $data->attachment = fileUpload( $request->attachment, 'backend/assets/files/employee/leave', '' );
            }

            if ( $request->attachment_two ) {
                if ( file_exists( $data->attachment_two ) ) {
                    File::delete( $data->attachment_two );
                }
                $data->attachment_two = fileUpload( $request->attachment_two, 'backend/assets/files/employee/leave', '' );
            }

            $data->save();

            DB::commit();
            return redirect()->back()->with( 'success', 'Employee leave application updated successfully.' );
        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'Failed to update user details', ['error' => $e->getMessage()] );
            return redirect()->back()->with( 'error', 'Oh ! Something went wrong !' );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( string $id ) {

        $data = LeaveApplication::find( $id );

        if ( $data->attachment || $data->attachment_two ) {

            if ( file_exists( $data->attachment ) ) {
                File::delete( $data->attachment );
            }

            if ( file_exists( $data->attachment_two ) ) {
                File::delete( $data->attachment_two );
            }
        }
        $data->delete();
        return redirect()->back()->with( 'success', 'Employee Attendance deleted successfully.' );

    }

    // public function createLeaveApplication( Request $request ) {

    //     $employee    = Employee::with( 'leaveTable' )->find( $request->id );
    //     $departments = Department::with( 'employees' )->get();
    //     $leaveTypes  = LeaveType::all();
    //     $users       = User::get( ['id', 'name', 'type'] );

    //     $view = 'Attendance::leave_application';
    //     if ( !view()->exists( $view ) ) {
    //         return view( 'errors.404' );
    //     } else {
    //         return view( 'Attendance::leave_application', compact( 'employee', 'departments', 'leaveTypes', 'users' ) );
    //     }
    // }
}
