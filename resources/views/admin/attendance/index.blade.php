@extends('layouts.app')
@section('title', '| Attendance')
@section('header', 'Attendance')
@section('content')
    @push('css')
        <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 32px !important;
                color: var(--black) !important;
            }

            .select2-container--default .select2-selection--single {
                min-height: 35px;
            }

            .select2-container .select2-selection--single {
                height: 35px;
                font-size: 14px;
                font-weight: 400;
                color: var(--black) !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 20px;
                position: absolute;
                top: 7px;
                right: 7px;
                width: 20px;
            }
        </style>
    @endpush
    <div class="dd-content">
        <!-- Breadcrumb Start Here -->
        <section class="breadcrumb-section m-0">
            <div class="container-fluid">
                <div class="row">
                    {{-- <div class="col-lg-3">
                        <div class="breadcrumb-wrapper">
                            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
                                aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Employee</li>
                                </ol>
                            </nav>
                        </div>
                    </div> --}}

                    <div class="col-lg-6">
                        <form action="{{ route('attendance.index') }}" method="GET" id="searchForm">
                            <div class="row">
                                <div class=" mb-3 col-lg-4">
                                    <div class="form-group">
                                        <select class="form-select from-select2 form-control-sm" id="employee_id"
                                            type="text" name="employee_id">
                                            <option value="">All Employee</option>
                                            @foreach ($departments as $department)
                                                <optgroup label="{{ $department?->name }}">
                                                    @foreach ($department->employees as $employee)
                                                        <option value="{{ $employee->id }}"
                                                            {{ request()->get('employee') == $employee->id ? 'selected' : '' }}>
                                                            {{ $employee->name }}
                                                        </option>
                                                    @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 col-lg-6">
                                    <div id="reportrange" class="custome-date-range form-control form-control-sm">
                                        <i class="fa fa-calendar-alt"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down"></i>
                                        <input type="hidden" name="start_date"
                                            value="{{ request()->get('start_date', startDateOfMonth()) }}" id="start_date">
                                        <input type="hidden" name="end_date"
                                            value="{{ request()->get('end_date', endDateOfMonth()) }}" id="end_date">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-6">
                        <div class="">
                            <div class="d-flex justify-content-end mb-3">

                                <button type="button" data-bs-toggle="modal" data-bs-target="#attendanceModal"
                                    class="btn btn-base btn-sm">
                                    Add Attendance
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
        </section>
        <!-- Breadcrumb End Here -->



        <section class="employee-section mb-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card table-v1 table-responsive">
                            <div class="card-header">
                                <h5 class="card-title">Attendance List</h5>
                            </div>
                            <hr>

                            <table id="" class="table text-center table-hover  table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Employee</th>
                                        <th>date</th>
                                        <th>Day</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        {{-- <th>Break Time Out</th>
                                        <th>Break Time In</th> --}}
                                        <th>Working Hours</th>
                                        <th>Normal Hours</th>
                                        <th>Overtime Hours</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($attendances as $key => $attendance)
                                        <tr>
                                            <td width="5%">{{ $key + 1 }}</td>
                                            <td>{{ $attendance->employee->name }}</td>
                                            <td width="15%">{{ normal_format_date($attendance->date) }}</td>
                                            <td width="10%">{{ $attendance ? $attendance->day : '-' }}</td>
                                            <td width="10%" style="background: #3c965417;">
                                                {{ $attendance ? $attendance->in_time : '-' }}</td>
                                            <td width="10%" style="background: #963c3c17;">
                                                {{ $attendance ? $attendance->out_time : '-' }}</td>
                                            {{-- <td width="10%">
                                                {{ $attendance->break_start_time ? $attendance->break_start_time : '-' }}
                                            </td>
                                            <td width="10%">
                                                {{ $attendance->break_end_time ? $attendance->break_end_time : '-' }}</td> --}}
                                            <td width="10%">
                                                {{ $attendance->working_hours ? formatTime($attendance->working_hours) : '-' }}
                                            </td>
                                            <td width="10%">
                                                {{ $attendance->normal_hours ? formatTime($attendance->normal_hours) : '-' }}
                                            </td>
                                            <td width="10%">
                                                {{ $attendance->overtime_hours ? formatTime($attendance->overtime_hours) : '-' }}
                                            </td>
                                            <td width="10%">
                                                @if ($attendance->status == 'present')
                                                    <span class="mx-auto badge rounded-pill text-bg-success">
                                                        Present
                                                        @if (date('H:i', strtotime($attendance->in_time)) > '10:00')
                                                            <span class="badge rounded-pill text-bg-warning">Late</span>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="badge rounded-pill text-bg-danger">Absent</span>
                                                @endif
                                            </td>

                                            <td width="10%">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <button class="btn btn-outline-base btn-sm edit"
                                                        data-id="{{ $attendance->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#editModal">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('attendance.delete', $attendance->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm delete">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                </tbody>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="13">No Data Found
                                    </td>
                                </tr>
                                @endforelse

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--New Attendance Modal -->
        <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            Attendance
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('attendance.store') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="mb-2 form--label text--white">Employee</label>
                                <span class="text-danger">*</span>
                                <div>
                                    <select class="form-select from-select2 form-control-sm" id="employee"
                                        name="employee_id" required style="width: 100%">
                                        @foreach ($departments as $department)
                                            <optgroup label="{{ $department?->name }}">
                                                @foreach ($department->employees as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        {{ request()->get('employee') == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->name }}
                                                    </option>
                                                @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="mb-2 form--label text--white">Date</label>
                                <span class="text-danger">*</span>
                                <div>
                                    <input type="text" name="date" id="date"
                                        class="form-control form-control-sm" required />
                                </div>
                            </div>

                            <div class="datepair row">
                                <div class="form-group mb-3 col-lg-6">
                                    <label class="mb-2 form--label text--white" for="in_time">In Time</label>
                                    <div>
                                        <input type="text" name="in_time" id="in_time"
                                            class="input time start ui-timepicker-input valid form-control form-control-sm"
                                            autocomplete="off" required />
                                    </div>

                                </div>

                                <div class="form-group mb-3 col-lg-6">
                                    <label class="mb-2 form--label text--white">out_time</label>
                                    <input type="text" name="out_time" id="out_time"
                                        class="input time end ui-timepicker-input form-control form-control-sm"
                                        autocomplete="off" />
                                </div>
                            </div>

                            {{-- <div class="datepair row">
                                <div class="form-group mb-3 col-lg-6">
                                    <label class="mb-2 form--label text--white">Break Time start</label>
                                    <div>
                                        <input type="text" name="break_start_time" id="break_start_time"
                                            class="input time start ui-timepicker-input valid form-control form-control-sm"
                                            autocomplete="off" />
                                    </div>

                                </div>

                                <div class="form-group mb-3 col-lg-6">
                                    <label class="mb-2 form--label text--white">Break Time end</label>
                                    <input type="text" name="break_end_time" id="break_end_time"
                                        class="input time end ui-timepicker-input form-control form-control-sm"
                                        autocomplete="off" />
                                </div>
                            </div> --}}

                            <div class="mb-3">
                                <label for="input4" class="form-label">Remarks</label>
                                <textarea type="text" name="remarks" rows="5" class="form-control" id="input3" placeholder="Remarks">{{ old('remarks') }}</textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- / Modal -->

        <!--Edit Attendance Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            Attendance
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="edit_section">

                    </div>
                </div>
            </div>
        </div>
        <!-- / Modal -->

    </div>

    @push('js')
        <script>
            $('body').on('click', '.edit', function() {
                var id = $(this).data('id');
                $.get("{{ url('admin/attendance/edit') }}/" + id,
                    function(data) {
                        $('#edit_section').html(data);
                    })
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#searchForm').on('change', function() {
                    $('#searchForm').submit();
                });
            });
        </script>

        <script type="text/javascript">
            $(function() {
                // Get start and end dates from request parameters and handle them safely
                var start = '{{ $startDate ?? now()->startOfMonth()->format('Y-m-d') }}';
                var end = '{{ $endDate ?? now()->endOfMonth()->format('Y-m-d') }}';

                // Convert to moment objects
                var startDate = moment(start, 'YYYY-MM-DD');
                var endDate = moment(end, 'YYYY-MM-DD');

                // Function to update the displayed text
                function cb(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }

                $('#reportrange').daterangepicker({
                    startDate: startDate,
                    endDate: endDate,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')],
                        'This Year': [moment().startOf('year'), moment().endOf('year')],
                        'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year')
                            .endOf('year')
                        ]
                    }
                }, cb);

                cb(startDate, endDate);
            });


            $("#reportrange").on('apply.daterangepicker', function(ev, picker) {

                var start = picker.startDate.format('YYYY/MM/DD');
                var end = picker.endDate.format('YYYY/MM/DD');
                $("#start_date").val(start);
                $("#end_date").val(end);

                $('#searchForm').submit();
            });

            $(document).ready(function() {
                $(".from-select2").select2();
            });
        </script>

        {{-- <script>
            $(function() {
                // Get the initial date range from the input field
                var initialStartDate = $('input[name="start_date"]').val();
                var initialEndDate = $('input[name="end_date"]').val();

                $('input[name="date"]').daterangepicker({
                    startDate: moment(initialStartDate, 'DD/MM/YYYY'),
                    endDate: moment(initialEndDate, 'DD/MM/YYYY'),
                    singleDatePicker: false,
                    showDropdowns: true,
                    minYear: 1901,
                    maxYear: parseInt(moment().format('YYYY'), 10),
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
                }, function(start, end) {
                    $('input[name="date"]').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                });
            });
        </script> --}}

        <script>
            $(function() {
                $('#date').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    minYear: 1901,
                    maxYear: parseInt(moment().format('YYYY'), 10),
                    startDate: moment(),
                    locale: {
                        format: 'DD-MM-YYYY'
                    }
                });
            });
        </script>

        <script>
            $(document).ready(function(e) {
                // initialize input widgets first
                $(".time").timepicker({
                    showDuration: true,
                    timeFormat: "g:ia",
                    step: 15,
                });
                $(".datepair").datepair();
            });
        </script>
    @endpush
@endsection
