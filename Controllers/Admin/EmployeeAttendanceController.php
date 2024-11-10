<?php

namespace Addons\Attendance\Controllers\Admin;

use Addons\Attendance\Models\EmployeeAttendance;
use Addons\Employee\Models\Department;
use Addons\Employee\Models\Employee;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            $departments      = Department::withWhereHas( 'employees' )->get();
            $attendancesQuery = EmployeeAttendance::whereBetween( 'date', [datepicker_format_reverse( $startDate ), datepicker_format_reverse( $endDate )] );

            $employeeId = $request->input( 'employee_id' );

            if ( $employeeId ) {

                $employeeId = $request->input( 'employee_id' );

                $startDate = Carbon::parse( $startDate );
                $endDate   = Carbon::parse( $endDate );

                $daysInRange = $startDate->diffInDays( $endDate ) + 1;

                $dates = collect( range( 0, $daysInRange - 1 ) )->map( function ( $day ) use ( $startDate ) {
                    return $startDate->copy()->addDays( $day )->format( 'Y-m-d' );
                } );

                $attendances = $attendancesQuery->where( 'employee_id', $employeeId )
                    ->whereBetween( 'date', [datepicker_format_reverse( $startDate->startOfDay() ), datepicker_format_reverse( $endDate->endOfDay() )] )
                    ->with( 'employee', function ( $query ) {
                        $query->select( "id", "name", "employee_id", 'work_table_id' )
                            ->with( 'workTable', function ( $query ) {
                                $query->select( "id", "title", "daily_working_hours" )
                                    ->with( 'work_days:id,work_table_id,day_of_week,working_day,working_time_from,working_time_to' );
                            } );
                    } )
                    ->get()
                    ->keyBy( 'date' );

                $employee       = Employee::with( 'workTable' )->find( $employeeId );
                $attendanceData = $dates->map( function ( $date ) use ( $attendances, $employee ) {
                    $attendance = $attendances->get( $date );

                    $dayOfWeek = Carbon::parse( $date )->format( 'l' );

                    $workingHours = getWorkingHoursByDay( $employee->workTable->work_days, $dayOfWeek );

                    return [
                        'id'                => $attendance ? $attendance->id : null,
                        'date'              => $date,
                        'status'            => $attendance ? $attendance->status : null,
                        'remarks'           => $attendance ? $attendance->remarks : null,
                        'employee_id'       => $employee ? $employee->id : null,
                        'employee_name'     => $attendance && $attendance->employee ? $attendance->employee->name : $employee->name,
                        'day'               => dayName( $date ),
                        'in_time'           => $attendance ? $attendance->in_time : null,
                        'out_time'          => $attendance ? $attendance->out_time : null,
                        'break_start_time'  => $attendance ? $attendance->break_start_time : null,
                        'break_end_time'    => $attendance ? $attendance->break_end_time : null,
                        'working_hours'     => $attendance ? $attendance->working_hours : null,
                        'normal_hours'      => $attendance ? $attendance->normal_hours : null,
                        'overtime_hours'    => $attendance ? $attendance->overtime_hours : null,
                        'break_hours'       => $attendance ? $attendance->break_hours : null,
                        'total_hours'       => $attendance ? $attendance->employee?->WorkTable->daily_working_hours : null,
                        'working_time_from' => $workingHours['working_time_from'] ?? null,
                        'working_time_to'   => $workingHours['working_time_to'] ?? null,
                        'working_day'       => $workingHours['working_day'] ?? null,
                        'working_time'      => $workingHours['working_time'] ?? null,
                    ];
                } );

                DB::commit();
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
                return view( 'Attendance::index', compact( 'departments', 'attendances', 'employeeId', 'startDate', 'endDate' ) );
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
    // public function store( Request $request ) {

    //     $request->validate( [
    //         'employee_id'      => 'required',
    //         'date'             => 'required|date',
    //         'in_time'          => 'required',
    //         'out_time'         => 'nullable|after:in_time',
    //         'break_start_time' => 'nullable|after:in_time|before:break_end_time',
    //         'break_end_time'   => 'nullable|after:break_start_time',
    //         'remarks'          => 'nullable|string',
    //     ] );
    //     try {
    //         DB::beginTransaction();

    //         $data = EmployeeAttendance::where( 'employee_id', $request->employee_id )->where( 'date', format_date_only( $request->date ) )->first();
    //         if ( $data ) {
    //             return redirect()->back()->with( 'error', 'Employee Attendance already exists for ' . format_date( $request->date ) . '' );
    //         }
    //         $data                   = new EmployeeAttendance();
    //         $data->employee_id      = $request->employee_id;
    //         $data->date             = datepicker_format_reverse( $request->date );
    //         $data->day              = dayName( $request->date );
    //         $data->in_time          = $request->in_time;
    //         $data->out_time         = $request->out_time;
    //         $data->break_start_time = $request->break_start_time;
    //         $data->break_end_time   = $request->break_end_time;
    //         $data->working_hours    = workingHours( $request->in_time, $request->out_time, $request->break_start_time, $request->break_end_time );
    //         $data->normal_hours     = normalHours( $request->in_time, $request->out_time );
    //         $data->overtime_hours   = overTime( $request->in_time, $request->out_time );
    //         $data->break_hours      = breakHours( $request->break_start_time, $request->break_end_time );
    //         $data->status           = $request->in_time ? 'present' : 'absent';
    //         $data->auth_id          = auth()->user()->id;
    //         $data->remarks          = $request->remarks;
    //         $data->save();

    //         DB::commit();
    //         return redirect()->back()->with( 'success', 'Employee Attendance created successfully.' );
    //     } catch ( \Exception $e ) {
    //         DB::rollBack();
    //         Log::error( 'Failed to update user details', ['error' => $e->getMessage()] );
    //         // return redirect()->back()->with( 'error', 'Oh ! Something went wrong !' );
    //         return redirect()->back()->with( 'error', $e->getMessage() );
    //     }
    // }

    public function store( Request $request ) {

        $request->validate( [
            'employee_id' => 'required',
            'date'        => 'required|date',
            'in_time'     => 'required',
            'out_time'    => 'nullable|after:in_time',
            'remarks'     => 'nullable|string',
        ] );

        try {
            DB::beginTransaction();

            if ( !config( 'employee.enabled' ) ) {
                return redirect()->back()->with( 'error', 'Employee module not enabled!' );
            }

            $data = EmployeeAttendance::where( 'employee_id', $request->employee_id )
                ->where( 'date', datepicker_format_reverse( $request->date ) )
                ->first();

            if ( $data ) {
                return redirect()->back()->with( 'error', 'Employee Attendance already exists for ' . format_date( $request->date ) );
            }

            $employee  = Employee::with( 'workTable.work_days' )->find( $request->employee_id );
            $workTable = $employee->workTable;

            $dayOfWeek = Carbon::parse( $request->date )->format( 'l' );
            $workDay   = collect( $workTable->work_days )->firstWhere( 'day_of_week', $dayOfWeek );

            $normalWorkingHours = 0;
            $isNonWorkingDay    = false;

            if ( $workDay ) {
                if ( $workDay['working_day'] === 'full_day' ) {
                    $normalWorkingHours = ( $workDay['working_time_to'] && $workDay['working_time_from'] )
                    ? Carbon::parse( $workDay['working_time_to'] )->diffInMinutes( Carbon::parse( $workDay['working_time_from'] ) ) / 60
                    : 0;
                } elseif ( $workDay['working_day'] === 'non_working_day' ) {
                    $isNonWorkingDay = true;
                }
            }

            // Calculate hours based on attendance
            $totalWorkingHours = totalWorkingHours( $request->in_time, $request->out_time );
            $workingHours      = workingHours( $request->in_time, $request->out_time, $workDay['break_time_from'], $workDay['break_time_to'] );
            $normalHours       = normalHours( $totalWorkingHours, $normalWorkingHours );
            $overtimeHours     = overtimeHours( $totalWorkingHours, $normalWorkingHours );
            $breakHours        = breakHours( $workDay['break_time_from'], $workDay['break_time_to'] );

            // Create new attendance record
            $data                   = new EmployeeAttendance();
            $data->employee_id      = $request->employee_id;
            $data->date             = datepicker_format_reverse( $request->date );
            $data->day              = dayName( $request->date );
            $data->in_time          = $request->in_time;
            $data->out_time         = $request->out_time;
            $data->break_start_time = $request->break_start_time;
            $data->break_end_time   = $request->break_end_time;
            $data->working_hours    = $workingHours;
            $data->normal_hours     = $normalHours;
            $data->overtime_hours   = $overtimeHours;
            $data->break_hours      = $breakHours;
            $data->status           = $request->in_time ? 'present' : 'absent';
            $data->auth_id          = auth()->user()->id;
            $data->remarks          = $request->remarks;
            $data->save();

            DB::commit();
            return redirect()->back()->with( 'success', 'Employee Attendance created successfully.' );
        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'Failed to update user details', ['error' => $e->getMessage()] );
            return redirect()->back()->with( 'error', $e->getMessage() );
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
    public function update( Request $request, $id ) {
        $request->validate( [
            'employee_id'      => 'required',
            'date'             => 'required|date',
            'in_time'          => 'required',
            'out_time'         => 'nullable|after:in_time',
            'break_start_time' => 'nullable|after:in_time|before:break_end_time',
            'break_end_time'   => 'nullable|after:break_start_time',
            'remarks'          => 'nullable|string',
        ] );

        try {
            DB::beginTransaction();

            // Find the existing attendance record
            $attendance = EmployeeAttendance::findOrFail( $id );

            // Check if the attendance entry belongs to the correct employee
            if ( $attendance->employee_id != $request->employee_id ) {
                return redirect()->back()->with( 'error', 'You are not authorized to update this attendance record.' );
            }

            // Fetch employee's work table
            $employee  = Employee::with( 'workTable.work_days' )->find( $request->employee_id );
            $workTable = $employee->workTable;

            // Get working hours for the day of the week
            $dayOfWeek = Carbon::parse( $request->date )->format( 'l' );
            $workDay   = collect( $workTable->work_days )->firstWhere( 'day_of_week', $dayOfWeek );

            // Initialize variables
            $normalWorkingHours = 0;

            // Determine normal working hours based on work day type
            if ( $workDay ) {
                if ( $workDay['working_day'] === 'full_day' ) {
                    $normalWorkingHours = ( $workDay['working_time_to'] && $workDay['working_time_from'] )
                    ? Carbon::parse( $workDay['working_time_to'] )->diffInMinutes( Carbon::parse( $workDay['working_time_from'] ) ) / 60
                    : 0;
                }
            }

            // Calculate hours based on attendance
            $totalWorkingHours = totalWorkingHours( $request->in_time, $request->out_time );
            $workingHours      = workingHours( $request->in_time, $request->out_time, $workDay['break_time_from'], $workDay['break_time_to'] );
            $normalHours       = normalHours( $totalWorkingHours, $normalWorkingHours );
            $overtimeHours     = overtimeHours( $totalWorkingHours, $normalWorkingHours );
            $breakHours        = breakHours( $workDay['break_time_from'], $workDay['break_time_to'] );

            // Update attendance record
            $attendance->employee_id      = $request->employee_id;
            $attendance->date             = datepicker_format_reverse( $request->date );
            $attendance->day              = dayName( $request->date );
            $attendance->in_time          = $request->in_time;
            $attendance->out_time         = $request->out_time;
            $attendance->break_start_time = $request->break_start_time;
            $attendance->break_end_time   = $request->break_end_time;
            $attendance->working_hours    = $workingHours; // Formatted as H:i
            $attendance->normal_hours     = $normalHours; // Formatted as H:i
            $attendance->overtime_hours   = $overtimeHours; // Formatted as H:i
            $attendance->break_hours      = $breakHours; // Formatted as H:i
            $attendance->status           = $request->in_time ? 'present' : 'absent';
            $attendance->auth_id          = auth()->user()->id;
            $attendance->remarks          = $request->remarks;
            $attendance->save();

            DB::commit();
            return redirect()->back()->with( 'success', 'Employee Attendance updated successfully.' );
        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'Failed to update attendance', ['error' => $e->getMessage()] );
            return redirect()->back()->with( 'error', $e->getMessage() );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( string $id ) {

        $data = EmployeeAttendance::find( $id );
        $data->delete();
        return redirect()->back()->with( 'success', 'Employee Attendance deleted successfully.' );

    }

    public function checkInCheckOut( Request $request ) {
        try {
            DB::beginTransaction();

            $attendance = EmployeeAttendance::where( 'employee_id', authEmployeeId() )
                ->where( 'date', datepicker_format_reverse( now() ) )
                ->first();

            $currentTime = now();

            $employee  = Employee::with( 'workTable.work_days' )->find( authEmployeeId() );
            $workTable = $employee->workTable;

            $dayOfWeek = Carbon::parse( today() )->format( 'l' );
            $workDay   = collect( $workTable->work_days )->firstWhere( 'day_of_week', $dayOfWeek );

            $normalWorkingHours = 0;

            if ( $workDay ) {
                if ( $workDay['working_day'] === 'full_day' ) {
                    $normalWorkingHours = ( $workDay['working_time_to'] && $workDay['working_time_from'] )
                    ? Carbon::parse( $workDay['working_time_to'] )->diffInMinutes( Carbon::parse( $workDay['working_time_from'] ) ) / 60
                    : 0;
                }
            }

            if ( !$attendance ) {

                $attendance              = new EmployeeAttendance();
                $attendance->employee_id = authEmployeeId();
                $attendance->date        = datepicker_format_reverse( now() );
                $attendance->day         = dayName( now() );
                $attendance->in_time     = format_date_only_time( $currentTime );
                $attendance->status      = 'present';
                $attendance->auth_id     = auth()->user()->id;
                $attendance->save();
                $message = 'Check In successfully.';
            } else {
                if ( $attendance->out_time ) {
                    return redirect()->back()->with( 'error', 'You have already checked out for ' . format_date( $request->date ) );
                }

                $attendance->out_time       = format_date_only_time( $currentTime );
                $attendance->working_hours  = workingHours( $attendance->in_time, $attendance->out_time, $attendance->break_start_time, $attendance->break_end_time );
                $attendance->normal_hours   = normalHours( $attendance->working_hours, $normalWorkingHours );
                $attendance->overtime_hours = overtimeHours( $attendance->working_hours, $normalWorkingHours );
                $attendance->break_hours    = breakHours( $attendance->break_start_time, $attendance->break_end_time );
                $attendance->save();
                $message = 'Check Out successfully.';
            }

            DB::commit();
            return redirect()->back()->with( 'success', $message );
        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'Failed to update attendance', ['error' => $e->getMessage()] );
            return redirect()->back()->with( 'error', $e->getMessage() );
        }
    }

    public function myAttendance( Request $request ) {
        $year  = $request->input( 'year' ) ?? date( 'Y' );
        $month = $request->input( 'month' ) ?? date( 'm' );

        $myAttendance = EmployeeAttendance::where( [
            'employee_id' => authEmployeeId(),
        ] )
            ->whereYear( 'date', $year )
            ->whereMonth( 'date', $month )
            ->get();

        return view( 'Attendance::my_attendance', compact( 'myAttendance' ) );
    }
}