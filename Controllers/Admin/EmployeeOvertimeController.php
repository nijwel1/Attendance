<?php

namespace Addons\Attendance\Controllers\Admin;

use Addons\Attendance\Models\EmployeeOvertime;
use Addons\Employee\Models\Department;
use Addons\Employee\Models\Overtime;
use App\Http\Controllers\Controller;
use App\Mail\ResignationMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmployeeOvertimeController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request ) {
        try {
            $employeeId = $request->employee_id ?? null;
            $startDate  = $request->input( 'start_date' ) ?? startDateOfMonth();
            $endDate    = $request->input( 'end_date' ) ?? endDateOfMonth();

            $employee_overtimes = EmployeeOvertime::with( 'employee:id,name,employee_id,department_id', 'user:id,name', 'overtime:id,format' )
                ->when( $employeeId, function ( $query ) use ( $employeeId ) {
                    $query->where( 'employee_id', $employeeId );
                } )

                ->when( $startDate && $endDate, function ( $query ) use ( $startDate, $endDate ) {
                    $query->whereBetween( 'date', [$startDate, $endDate] );
                } )
                ->latest()->get();

            $overtime_formats = Overtime::get( ['id', 'format'] );
            $departments      = Department::with( ['employees' => function ( $query ) {
                $query->select( 'id', 'name', 'department_id' );
            }] )->get();

            $users = User::get( ['id', 'name', 'type'] );

            $view = "OverTimeRecord::index";

            if ( !view()->exists( $view ) ) {
                return view( 'errors.404' );
            } else {
                return view( 'OverTimeRecord::index', compact( 'employee_overtimes', 'overtime_formats', 'departments', 'users', 'startDate', 'endDate' ) );
            }

        } catch ( \Exception $e ) {
            return redirect()->back()->with( 'error', $e->getMessage() );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( Request $request ) {
        $request->validate( [
            'employee_id' => 'required|numeric',
            'ot_format'   => 'required|numeric',
            'hours_dates' => 'required|numeric',
            'date'        => 'required|date',
            'month'       => 'required',
            'remark'      => 'nullable|string|max:255',
            'status'      => 'required|in:Pending,Canceled,Approved,Rejected',
            'email_to'    => 'required',
        ], [
            'ot_format.required' => 'Overtime Format is required',
            'employee_id.numeric' => 'Employee not found',
            'ot_format.numeric' => 'Overtime format not found',
        ] );
        try {
            DB::beginTransaction();
            //formate date
            $date = Carbon::parse( $request->date )->format( 'Y-m-d' );

            //impload email ides
            $email_ids = implode( ',', $request->email_to );

            //insert data into table
            $overtime              = new EmployeeOvertime;
            $overtime->employee_id = $request->employee_id;
            $overtime->auth_id     = Auth::id();
            $overtime->overtime_id = $request->ot_format;
            $overtime->hours_dates = $request->hours_dates;
            $overtime->date        = $date;
            $overtime->month       = $request->month;
            $overtime->remarks     = $request->remark;
            $overtime->status      = $request->status;
            $overtime->email_to    = $email_ids;
            $overtime->save();

            //Fetch emails for the user IDs provided in the request
            $emailIds = $request->email_to;
            $emails   = User::whereIn( 'id', $emailIds )->pluck( 'email' )->toArray(); // Get email addresses

            //store data in a variable
            $emailData = $overtime;

            //send mail
            foreach ( $emails as $email ) {
                Mail::to( $email )->send( new ResignationMail( $emailData ) );
            }

            DB::commit();
            return redirect()->back()->with( 'success', 'Created successfully' );
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
    public function edit( string $id ) {
        $data             = EmployeeOvertime::find( $id );
        $overtime_formats = Overtime::get( ['id', 'format'] );

        $departments = Department::with( ['employees' => function ( $query ) {
            $query->select( 'id', 'name', 'department_id' );
        }] )->get();

        $view = "OverTimeRecord::edit";

        if ( !view()->exists( $view ) ) {
            return view( 'errors.404' );
        } else {
            return view( 'OverTimeRecord::edit', compact( 'data', 'overtime_formats', 'departments' ) );
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request ) {
        $request->validate( [
            'employee_id' => 'required',
            'ot_format'   => 'required',
            'hours_dates' => 'required|numeric',
            'date'        => 'required|date',
            'month'       => 'required',
            'remark'      => 'nullable|string|max:255',
            'status'      => 'required|in:Pending,Canceled,Approved,Rejected',
        ] );
        try {
            DB::beginTransaction();
            //formate date
            $date = Carbon::parse( $request->date )->format( 'Y-m-d' );
            //insert data into table
            $overtime              = EmployeeOvertime::findOrFail( $request->id );
            $overtime->employee_id = $request->employee_id;
            $overtime->auth_id     = Auth::id();
            $overtime->overtime_id = $request->ot_format;
            $overtime->hours_dates = $request->hours_dates;
            $overtime->date        = $date;
            $overtime->month       = $request->month;
            $overtime->remarks     = $request->remark;
            $overtime->status      = $request->status;
            $overtime->save();

            DB::commit();
            return redirect()->back()->with( 'success', 'Updated successfully' );
        } catch ( \Exception $e ) {
            return redirect()->back()->with( 'error', $e->getMessage() );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( string $id ) {
        $overtime = EmployeeOvertime::findOrFail( $id );
        $overtime->delete();
        return redirect()->back()->with( 'success', 'Deleted successfully' );
    }

    /**
     * Remove the selected resource from storage.
     */
    public function deleteSelected( Request $request ) {
        try {
            $ids = $request->input( 'ids' );

            if ( !empty( $ids ) ) {
                // Delete the selected terminations
                EmployeeOvertime::whereIn( 'id', $ids )->delete();

                return response()->json( ['success' => 'Terminations deleted successfully.'] );
            }

            return response()->json( ['error' => 'No termination selected.'], 400 );
        } catch ( \Exception $e ) {
            return response()->json( ['error' => $e->getMessage()], 400 );
        }
    }
}
