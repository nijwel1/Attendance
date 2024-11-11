<?php

namespace Addons\Attendance\Controllers\Admin;

use Addons\Attendance\Models\LeaveEncashment;
use Addons\Employee\Models\Department;
use Addons\Employee\Models\Employee;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LeaveEncashmentController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request ) {
        $departments = Department::with( 'employees' )->get();
        $users       = User::all();
        $employee    = Employee::first();

        if ( $request->year ) {
            $leave_encashments = LeaveEncashment::with( 'employee:id,name,employee_id' )
                ->where( 'month_to_apply', "like", "%/" . $request->year )
                ->latest()
                ->get();
        } else {
            $leave_encashments = LeaveEncashment::with( 'employee:id,name,employee_id' )->latest()->get();
        }

        $view = 'LeaveEncashment::index';

        if ( !view()->exists( $view ) ) {
            return view( 'errors.404' );
        } else {
            return view( 'LeaveEncashment::index', compact( 'departments', 'users', 'employee', 'leave_encashments' ) );
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create( Request $request ) {
        $employee          = Employee::with( 'leaveTable', 'leaveApplication' )->find( $request->id );
        $departments       = Department::withWhereHas( 'employees' )->get();
        $users             = User::get( ['id', 'name', 'type'] );
        $leave_encashments = LeaveEncashment::where( 'employee_id', $request->id )->get();
        $view              = 'LeaveEncashment::create';

        if ( !view()->exists( $view ) ) {
            return view( 'errors.404' );
        } else {
            return view( $view, compact( 'employee', 'departments', 'users', 'leave_encashments' ) );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( Request $request ) {
        $request->validate( [
            'employee_id'       => 'required',
            'encashment_day'    => 'required|max:11',
            'encashment_amount' => 'required|max:11',
            'month_to_apply'    => 'required',
            'remarks'           => 'string|nullable|max:255',
            'email_to'          => 'required',
            'status'            => 'required',
        ] );
        try {
            DB::beginTransaction();

            $leave_encashment                    = new LeaveEncashment();
            $leave_encashment->auth_id           = Auth::user()->id;
            $leave_encashment->employee_id       = $request->employee_id;
            $leave_encashment->leave_table_id    = $request->leave_table_id;
            $leave_encashment->leave_type        = "annual";
            $leave_encashment->encashment_day    = $request->encashment_day;
            $leave_encashment->encashment_amount = $request->encashment_amount;
            $leave_encashment->month_to_apply    = $request->month_to_apply;
            $leave_encashment->remarks           = $request->remarks;
            $leave_encashment->email_to          = implode( ',', $request->email_to );
            $leave_encashment->status            = $request->status;

            if ( $request->attachment ) {
                $leave_encashment->attachment = fileUpload( $request->attachment, 'backend/assets/files/employee/leave', '' );
            }
            $leave_encashment->save();

            DB::commit();
            return redirect()->back()->with( 'success', 'Leave Encashment created successfully.' );
        } catch ( \Exception $e ) {
            return redirect()->back()->with( 'error', $e->getMessage() );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( string $id ) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( Request $request, string $id ) {
        $departments       = Department::withWhereHas( 'employees' )->get();
        $users             = User::get( ['id', 'name', 'type'] );
        $leave_encashments = LeaveEncashment::where( 'employee_id', $request->id )->get();
        $enceshment        = LeaveEncashment::find( $id );
        $employee          = Employee::with( 'leaveTable', 'leaveApplication' )->find( $enceshment->employee_id );
        $email_to          = explode( ',', $enceshment->email_to );
        $view              = 'LeaveEncashment::edit';

        if ( !view()->exists( $view ) ) {
            return view( 'errors.404' );
        } else {
            return view( $view, compact( 'employee', 'departments', 'users', 'leave_encashments', 'enceshment', 'email_to' ) );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, string $id ) {
        $request->validate( [
            'employee_id'       => 'required',
            'encashment_day'    => 'required',
            'encashment_amount' => 'required',
            'month_to_apply'    => 'required',
            'remarks'           => 'string|nullable|max:255',
            'email_to'          => 'required',
            'status'            => 'required',
        ] );
        try {
            DB::beginTransaction();

            $leave_encashment                    = LeaveEncashment::find( $id );
            $leave_encashment->auth_id           = Auth::user()->id;
            $leave_encashment->employee_id       = $request->employee_id;
            $leave_encashment->leave_table_id    = $request->leave_table_id;
            $leave_encashment->leave_type        = "annual";
            $leave_encashment->encashment_day    = $request->encashment_day;
            $leave_encashment->encashment_amount = $request->encashment_amount;
            $leave_encashment->month_to_apply    = $request->month_to_apply;
            $leave_encashment->remarks           = $request->remarks;
            $leave_encashment->email_to          = implode( ',', $request->email_to );
            $leave_encashment->status            = $request->status;

            if ( $request->attachment ) {
                if ( file_exists( $leave_encashment->attachment ) ) {
                    File::delete( $leave_encashment->attachment );
                }
                $leave_encashment->attachment = fileUpload( $request->attachment, 'backend/assets/files/employee/leave', '' );
            }

            $leave_encashment->save();

            DB::commit();
            return redirect()->back()->with( 'success', 'Leave Encashment updated successfully.' );
        } catch ( \Exception $e ) {
            return redirect()->back()->with( 'error', $e->getMessage() );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( string $id ) {
        $data = LeaveEncashment::find( $id );

        if ( $data->attachment ) {
            if ( file_exists( $data->attachment ) ) {
                File::delete( $data->attachment );
            }
        }
        $data->delete();
        return redirect()->back()->with( 'success', 'Leave Encashment deleted successfully.' );
    }

    public function deleteAll( Request $request ) {
        $leaveEncashments = LeaveEncashment::whereIn( 'id', $request->id )->get();

        foreach ( $leaveEncashments as $leaveEncashment ) {
            // Check if the attachment file exists and delete it
            if ( $leaveEncashment->attachment && file_exists( public_path( $leaveEncashment->attachment ) ) ) {
                File::delete( public_path( $leaveEncashment->attachment ) );
            }
        }
        LeaveEncashment::whereIn( 'id', $request->id )->delete();

        return redirect()->back()->with( 'success', 'Selected Leave Encashments and their attachments deleted successfully.' );
    }
}
