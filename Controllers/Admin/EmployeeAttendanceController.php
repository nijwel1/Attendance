<?php

namespace Addons\Attendance\Controllers\Admin;

use Addons\Attendance\Models\EmployeeAttendance;
use Addons\Employee\Models\Department;
use Addons\Employee\Models\Employee;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeAttendanceController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request ) {
        try {
            DB::beginTransaction();

            $startDate = $request->input( 'start_date' ) ?? startDateOfMonth();
            $endDate   = $request->input( 'end_date' ) ?? endDateOfMonth();

            $departments      = Department::with( 'employees' )->get();
            $attendancesQuery = EmployeeAttendance::whereBetween( 'date', [$startDate, $endDate] );

            $employeeId = $request->input( 'employee_id' );

            if ( $employeeId ) {

                $employeeId = $request->input( 'employee_id' );

                // Parse the start and end dates
                $startDate = Carbon::parse( $startDate );
                $endDate   = Carbon::parse( $endDate );

                // Calculate the number of days between the start and end dates
                $daysInRange = $startDate->diffInDays( $endDate ) + 1;

                // Create an array of all dates between the start and end dates
                $dates = collect( range( 0, $daysInRange - 1 ) )->map( function ( $day ) use ( $startDate ) {
                    return $startDate->copy()->addDays( $day )->format( 'Y/m/d' );
                } );

                // Fetch attendance data for the employee for the specified date range
                $attendances = $attendancesQuery->where( 'employee_id', $employeeId )
                    ->whereBetween( 'date', [format_date_only( $startDate->startOfDay() ), format_date_only( $endDate->endOfDay() )] )
                    ->get()
                    ->keyBy( 'date' ); // Key by date for easy lookup

                // Prepare attendance data for the view
                $attendanceData = $dates->map( function ( $date ) use ( $attendances ) {
                    $attendance = $attendances->get( $date );
                    $employee   = Employee::select( 'id', 'name' )->find( request()->input( 'employee_id' ) );

                    return [
                        'date'             => $date,
                        'status'           => $attendance ? $attendance->status : null,
                        'remarks'          => $attendance ? $attendance->remarks : null,
                        'employee_id'      => $attendance ? $attendance->employee_id : null,
                        'employee_name'    => $attendance && $attendance->employee ? $attendance->employee->name : $employee->name,
                        'day'              => dayName( $date ),
                        'in_time'          => $attendance ? $attendance->in_time : null,
                        'out_time'         => $attendance ? $attendance->out_time : null,
                        'break_start_time' => $attendance ? $attendance->break_start_time : null,
                        'break_end_time'   => $attendance ? $attendance->break_end_time : null,
                        'working_hours'    => $attendance ? $attendance->working_hours : null,
                        'normal_hours'     => $attendance ? $attendance->normal_hours : null,
                        'overtime_hours'   => $attendance ? $attendance->overtime_hours : null,
                        'break_hours'      => $attendance ? $attendance->break_hours : null,
                    ];
                } );

                // Commit DB transaction (if needed)
                DB::commit();

                // Return the view with departments and attendance data

                $view = 'Attendance::individual';
                if ( !view()->exists( $view ) ) {
                    return view( 'errors.404' );
                } else {
                    return view( 'Attendance::individual', compact( 'departments', 'attendanceData', 'employeeId', 'startDate', 'endDate' ) );
                }
            }

            $attendances = $attendancesQuery->get();

            DB::commit();

            $view = 'Attendance::index';
            if ( !view()->exists( $view ) ) {
                return view( 'errors.404' );
            } else {
                return view( 'Attendance::index', compact( 'departments', 'attendances', 'employeeId' ) );
            }

        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'Failed to update user details', ['error' => $e->getMessage()] );
            return redirect()->back()->with( 'error', 'Oh! Something went wrong!' );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( Request $request ) {

        $request->validate( [
            'employee_id'      => 'required',
            'date'             => 'required|date',
            'in_time'          => 'required|date_format:h:ia',
            'out_time'         => 'nullable|after:in_time',
            'break_start_time' => 'nullable|after:in_time|before:break_end_time',
            'break_end_time'   => 'nullable|after:break_start_time',
            'remarks'          => 'nullable|string',
        ] );
        try {
            DB::beginTransaction();

            $data = EmployeeAttendance::where( 'employee_id', $request->employee_id )->where( 'date', format_date_only( $request->date ) )->first();
            if ( $data ) {
                return redirect()->back()->with( 'error', 'Employee Attendance already exists for ' . format_date( $request->date ) . '' );
            }
            $data                   = new EmployeeAttendance();
            $data->employee_id      = $request->employee_id;
            $data->date             = format_date_only( $request->date );
            $data->day              = dayName( $request->date );
            $data->in_time          = $request->in_time;
            $data->out_time         = $request->out_time;
            $data->break_start_time = $request->break_start_time;
            $data->break_end_time   = $request->break_end_time;
            $data->working_hours    = workingHours( $request->in_time, $request->out_time, $request->break_start_time, $request->break_end_time );
            $data->normal_hours     = normalHours( $request->in_time, $request->out_time );
            $data->overtime_hours   = overTime( $request->in_time, $request->out_time );
            $data->break_hours      = breakHours( $request->break_start_time, $request->break_end_time );
            $data->status           = $request->in_time ? 'present' : 'absent';
            $data->auth_id          = auth()->user()->id;
            $data->remarks          = $request->remarks;
            $data->save();

            DB::commit();
            return redirect()->back()->with( 'success', 'Employee Attendance created successfully.' );
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

            $data        = EmployeeAttendance::find( $id );
            $departments = Department::with( 'employees' )->get();

            $view = 'Attendance::edit';
            if ( !view()->exists( $view ) ) {
                return view( 'errors.404' );
            } else {
                return view( 'Attendance::edit', compact( 'departments', 'data' ) );
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
            'employee_id'      => 'required',
            'date'             => 'required|date',
            'in_time'          => 'required|date_format:h:ia',
            'out_time'         => 'required|after:in_time',
            'break_start_time' => 'nullable|after:in_time|before:break_end_time',
            'break_end_time'   => 'nullable|after:break_start_time',
            'remarks'          => 'nullable|string',
        ] );
        // try {

        DB::beginTransaction();

        $data                   = EmployeeAttendance::find( $id );
        $data->employee_id      = $request->employee_id;
        $data->date             = format_date_only( $request->date );
        $data->day              = dayName( $request->date );
        $data->in_time          = $request->in_time;
        $data->out_time         = $request->out_time;
        $data->break_start_time = $request->break_start_time;
        $data->break_end_time   = $request->break_end_time;
        $data->working_hours    = workingHours( $request->in_time, $request->out_time, $request->break_start_time, $request->break_end_time );
        $data->normal_hours     = normalHours( $request->in_time, $request->out_time );
        $data->overtime_hours   = overTime( $request->in_time, $request->out_time );
        $data->break_hours      = breakHours( $request->break_start_time, $request->break_end_time );
        $data->status           = $request->in_time ? 'present' : 'absent';
        $data->auth_id          = auth()->user()->id;
        $data->remarks          = $request->remarks;
        $data->save();

        DB::commit();
        return redirect()->back()->with( 'success', 'Employee Attendance updated successfully.' );
        // } catch ( \Exception $e ) {
        //     DB::rollBack();
        //     Log::error( 'Failed to update user details', ['error' => $e->getMessage()] );
        //     return redirect()->back()->with( 'error', 'Oh ! Something went wrong !' );
        // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( string $id ) {

        $data = EmployeeAttendance::find( $id );
        $data->delete();
        return redirect()->back()->with( 'success', 'Employee Attendance deleted successfully.' );

    }
}
