@extends('layouts.app')
@section('title', '| Leave Application')
@section('header', 'Leave Application')
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
                    <div class="col-lg-3">
                        <div class="breadcrumb-wrapper">
                            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
                                aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Leave Application</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <form action="{{ route('leave_application.index') }}" method="GET" id="searchForm">
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
                                <div class=" mb-3 col-lg-4">
                                    <div class="form-group">
                                        <select class="form-select from-select2 form-control-sm" id="year"
                                            type="text" name="year">
                                            @foreach (yearsBefore(5) as $year)
                                                <option value="{{ $year }}"
                                                    {{ request()->get('year') == $year ? 'selected' : '' }}>
                                                    {{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-3">
                        <div class="">
                            <div class="d-flex justify-content-end mb-3">

                                <button type="button" data-bs-toggle="modal" data-bs-target="#leaveApplicationModal"
                                    class="btn btn-base btn-sm add-leave-application" data-id="{{ $employee->id }}">
                                    New Leave Application
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
                        <div class="card">
                            <div class="card-header mb-4">
                                <h5 class="card-title">Attendance List</h5>
                            </div>

                            <table id="" class="table table-hover table-responsive table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Number of days</th>
                                        <th>Type</th>
                                        <th>Month to pay</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                        <th>File</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($leaveApplications as $key => $leave)
                                        <tr>
                                            <td width="5%">{{ $key + 1 }}</td>
                                            <td width="15%">{{ $leave->employee?->name }} <br>
                                                ID: {{ $leave->employee?->id_number }}</td>
                                            <td width="10%">{{ normal_format_date($leave->date_from) }}</td>
                                            <td width="10%">{{ normal_format_date($leave->date_to) }}</td>
                                            <td width="10%">{{ $leave->number_of_days }}</td>
                                            <td width="10%">{{ $leave->leaveType?->title }}</td>
                                            <td width="10%">{{ $leave->month_to_pay }}</td>
                                            <td width="10%">{{ $leave->remarks }}</td>
                                            <td width="10%">{{ $leave->status }}</td>
                                            <td width="10%"></td>

                                            <td width="10%">
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-outline-base btn-sm edit"
                                                        data-id="{{ $leave->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#editModal">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('leave_application.delete', $leave->id) }}"
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

        <!--New leave application  Modal -->
        <div class="modal fade" id="leaveApplicationModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xxl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            New Leave Application
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="leave_application_section">

                    </div>
                </div>
            </div>
        </div>
        <!-- / Modal -->

        <!--Edit leave application Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xxl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            Leave Application Edit
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
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

        <script>
            $('body').on('click', '.edit', function() {
                var id = $(this).data('id');
                $.get("{{ url('admin/leave-application/edit') }}/" + id,
                    function(data) {
                        $('#edit_section').html(data);
                    })
            });
        </script>

        <script>
            $('body').on('click', '.add-leave-application', function test() {
                var id = $(this).data('id');
                var date = $(this).data('date');
                $.ajax({
                    url: "{{ url('admin/leave-application/create') }}",
                    type: 'GET',
                    data: {
                        id: id,
                        date: date
                    },
                    success: function(data) {
                        $('#leave_application_section').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching leave application:', error);
                    }
                });
            });

            $('body').on('change', '.employee_id', function() {
                var id = $(this).val();
                var date = $("#from_date").val();

                // Convert date from MM/DD/YYYY to YYYY/MM/DD
                var dateParts = date.split('/');
                if (dateParts.length === 3) {
                    date = dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0]; // YYYY/MM/DD
                }
                $.ajax({
                    url: "{{ url('admin/leave-application/create') }}",
                    type: 'GET',
                    data: {
                        id: id,
                        date: date
                    },
                    success: function(data) {
                        $('#leave_application_section').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching leave application:', error);
                    }
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#department').on('change', function() {
                    $('#searchForm').submit(); // Submit the form
                });
            });
        </script>

        <script type="text/javascript">
            $(function() {
                // Set default start and end dates to this month
                var start = moment().startOf('month');
                var end = moment().endOf('month');

                function cb(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }

                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    }
                }, cb);

                cb(start, end); // Call the callback function to set the initial text
            });


            $("#reportrange").on('apply.daterangepicker', function(ev, picker) {

            });

            $(document).ready(function() {
                $(".from-select2").select2();
            });
        </script>

        <script>
            $(function() {
                $('#date').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    minYear: 1901,
                    maxYear: parseInt(moment().format('YYYY'), 10),
                    locale: {
                        format: 'YYYY-MM-DD' // Change this to your desired format
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

            $(document).ready(function() {
                $('#searchForm').on('change', function() {
                    $('#searchForm').submit(); // Submit the form
                });
            });
        </script>
    @endpush
@endsection
