@extends('layouts.app')
@section('title', '| Employee Overtime')
@section('header', 'Employee Overtime')
@section('content')
    @push('css')
        <style>
            .datepicker {
                position: absolute;
                z-index: 1065 !important;
                opacity: 1;
            }

            .table-condensed {
                width: 100%
            }
        </style>
    @endpush
    <div class="dd-content">

        <!-- Breadcrumb Start Here -->
        <section class="breadcrumb-section m-0">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-lg-2">
                        <div class="breadcrumb-wrapper">
                            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
                                aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Employee Overtime</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <form action="{{ route('employee.overtime.index') }}" method="get" id="searchForm">
                            <div class="row">
                                <div class="col-lg-3 employee_id_filter_warp">
                                    <select name="employee_id"
                                        class="form-control select2 form-control-sm form-select-sm  employee_id_filter"
                                        id="filter_employee" required>
                                        <option value="" selected>All Employees</option>
                                        @foreach ($departments as $department)
                                            @if ($department->employees->isNotEmpty())
                                                <optgroup label="{{ $department?->name }}">
                                                    @foreach ($department->employees as $employee)
                                                        <option value="{{ $employee->id }}"
                                                            {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                            {{ $employee->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-5">
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

                    <div class="col-lg-2 d-flex justify-content-end mb-3 gap-3">
                        <div>
                            <button class="btn btn-danger btn-sm" id="deleteSelected">All Delete</button>
                        </div>
                        <div>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#createOvertimeModal"
                                class="btn btn-sm btn-base">
                                Add Overtime
                            </button>
                        </div>

                    </div>



                </div>
            </div>
        </section>
        <!-- Breadcrumb End Here -->

        <section class="employee-section mb-4">
            <div class="container-fluid">

                <!-- employee short info -->
                <div class="row gy-4 mb-4">
                    <div class="col-lg-12">
                        <div class="card table-responsive table-v2">
                            <table id="myTable7" class="table table-hover text-center  table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"> #</th>
                                        <th>Uniqe Id</th>
                                        <th>Employee</th>
                                        <th>No of Hours/Dates</th>
                                        <th>OT Pay</th>
                                        <th>Date</th>
                                        <th>Month To Apply</th>
                                        <th>Remark</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($employee_overtimes as $overtime)
                                        <tr>
                                            <td width="5%"><input type="checkbox" class="check"
                                                    data-id="{{ $overtime->id }}"> {{ $loop->iteration }}</td>
                                            <td>{{ $overtime?->employee?->employee_id }}</td>
                                            <td>{{ $overtime?->employee?->name }}</td>
                                            <td>{{ $overtime?->hours_dates }}</td>
                                            <td>{{ $overtime?->overtime?->format }}</td>
                                            <td>{{ datepicker_format($overtime?->date) }}</td>
                                            <td>{{ $overtime?->month }}</td>
                                            <td>{!! $overtime?->remarks !!}</td>
                                            <td>{{ $overtime?->status }}</td>
                                            <td width="10%">
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-outline-base btn-sm edit" data-bs-toggle="modal"
                                                        data-id="{{ $overtime->id }}" data-bs-target="#overtimeEditModal">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('employee.overtime.destroy', $overtime->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" id="delete"
                                                            class="btn btn-outline-danger btn-sm delete">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!--Create Modal -->
                        <div class="modal fade px-5" id="createOvertimeModal" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-top modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                                            Add Overtime
                                        </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('employee.overtime.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3 employee_id_wrap">
                                                <label for="employee_id" class="form-label">Employee</label>
                                                <select name="employee_id" class="form-control employee_id" id="employee_id"
                                                    required>
                                                    <option value="" selected disabled>Select Employee</option>
                                                    @foreach ($departments as $department)
                                                        @if ($department->employees->isNotEmpty())
                                                            <optgroup label="{{ $department?->name }}">
                                                                @foreach ($department->employees as $employee)
                                                                    <option value="{{ $employee->id }}"
                                                                        {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                                        {{ $employee->name }}</option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3 col-md-12 overtime_format_wrap">
                                                <label for="overtime_format" class="form-label">OT Pay </label>
                                                <select name="ot_format" id="overtime_format"
                                                    class="form-control overtime_format" required>
                                                    <option value="" selected disabled>Select OT Pay</option>
                                                    @foreach ($overtime_formats as $format)
                                                        <option value="{{ $format->id }}"
                                                            {{ old('ot_format') == $format->id ? 'selected' : '' }}>
                                                            {{ $format->format }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3 col-md-12">
                                                <label for="amount_capping" class="form-label">No of Hours/Dates</label>
                                                <input type="number" name="hours_dates"
                                                    value="{{ old('hours_dates') }}" id="amount_capping"
                                                    class="form-control number-input form-control-sm" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="date" class="form-label">Date</label>
                                                <input class="datepicker-here form-control" placeholder="Select date"
                                                    name="date" data-range="false" data-multiple-dates-separator=" - "
                                                    data-language="en" data-format="dd-mm-yyyy" readonly id="date"
                                                    data-position="bottom left" autocomplete="off"
                                                    value="{{ old('date') }}" required />
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="month" class="form-label">Month to Apply</label>
                                                <input type="text" id="month" class="form-control month"
                                                    placeholder="Select Month" name="month"
                                                    value="{{ old('month') }}" readonly autocomplete="off" required />
                                            </div>
                                            <div class="mb-3 col-md-12">
                                                <label for="description" class="form-label">Remark</label>
                                                <textarea class="form-control summernote" name="remark" id="" cols="10" rows="3">{{ old('remark') }}</textarea>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select name="status" class="form-control status" id="status"
                                                    required>
                                                    <option value="Pending" selected
                                                        {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="Canceled"
                                                        {{ old('status') == 'Canceled' ? 'selected' : '' }}>Canceled
                                                    </option>
                                                    <option value="Rejected"
                                                        {{ old('status') == 'Rejected' ? 'selected' : '' }}>Rejected
                                                    </option>
                                                    <option value="Approved"
                                                        {{ old('status') == 'Approved' ? 'selected' : '' }}>Approved
                                                    </option>
                                                </select>
                                            </div>

                                            @php
                                                $firstTypeOneSelected = false;
                                            @endphp
                                            <div class="col-lg-12 email_to_wrap">
                                                <div class="mb-3 d-flex flex-column">
                                                    <label for="email_to" class="form-label">Email To</label>
                                                    <select class="from-select w-100 email_to" name="email_to[]"
                                                        id="email_to" multiple="multiple" required>
                                                        @foreach ($users as $user)
                                                            @dump($user->type)
                                                            <option value="{{ $user->id }}"
                                                                @if (in_array($user->id, old('email_to', [])) || (!$firstTypeOneSelected && $user->type == 'admin')) @php $firstTypeOneSelected = true; @endphp
                                                                selected @endif>
                                                                {{ $user->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
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

                        <!--edit Modal -->
                        <div class="modal fade px-5" id="overtimeEditModal" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-top modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                                            Edit Overtime
                                        </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="edit_section">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- / Modal -->

                    </div>
                </div>
            </div>
        </section>
    </div>
    @push('js')
        <script>
            $('body').on('click', '.edit', function() {
                let id = $(this).data('id');
                $.get("{{ url('admin/employee-overtime/edit') }}/" + id,
                    function(data) {
                        $('#edit_section').html(data);
                    })
            })
        </script>
        <script>
            //select2 js
            $(document).ready(function() {
                $("#employee_id ,#overtime_format, #email_to ,#status").select2({
                    dropdownParent: $('#createOvertimeModal'),
                    tags: true,
                    tokenSeparators: [','],
                    width: '100%'
                });
            });

            $(".select2").select2();

            //month picker
            $(document).ready(function() {
                $('.month').datepicker({
                    format: "mm/yyyy",
                    startView: "months",
                    minViewMode: "months",
                    autoclose: true
                });
            });

            //select all
            $(document).ready(function() {
                // Select/Deselect all checkboxes
                $('#selectAll').change(function() {
                    $('.check').prop('checked', this.checked);
                });

                // If any checkbox is unchecked, uncheck the "Select All" checkbox
                $('.check').change(function() {
                    if (!this.checked) {
                        $('#selectAll').prop('checked', false);
                    }
                });
            });



            //Delete all selected
            $(document).ready(function() {
                $('#selectAll').change(function() {
                    $('.check').prop('checked', this.checked);
                });

                // If any checkbox is unchecked, uncheck the "Select All" checkbox
                $('.check').change(function() {
                    if (!this.checked) {
                        $('#selectAll').prop('checked', false);
                    }
                });

                // Delete selected terminations with SweetAlert confirmation
                $('#deleteSelected').click(function() {
                    // Collect IDs of selected terminations
                    let selectedIds = [];
                    $('.check:checked').each(function() {
                        selectedIds.push($(this).data('id'));
                    });

                    // Check if any IDs are selected
                    if (selectedIds.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No selection',
                            text: 'Please select at least one complaint to delete.',
                        });
                        return;
                    }

                    // Show SweetAlert confirmation
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If confirmed, proceed with the deletion
                            $.ajax({
                                url: '{{ route('employee.overtime.deleteSelected') }}',
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    ids: selectedIds
                                },
                                success: function(response) {
                                    // Show success message using Toastr
                                    toastr.success('complaint deleted successfully!',
                                        'Success', {
                                            timeOut: 3000, // Duration of the toaster message (in milliseconds)
                                            progressBar: true
                                        });

                                    // Reload the page after a short delay (e.g., 3 seconds)
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                },
                                error: function(xhr) {
                                    Swal.fire(
                                        'Error!',
                                        'An error occurred while trying to delete the selected complaints.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
            });


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

                var start = picker.startDate.format('YYYY-MM-DD');
                var end = picker.endDate.format('YYYY-MM-DD');
                $("#start_date").val(start);
                $("#end_date").val(end);

                $('#searchForm').submit();
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#searchForm').on('change', function() {
                    $('#searchForm').submit();
                });
            });
        </script>
    @endpush
@endsection
