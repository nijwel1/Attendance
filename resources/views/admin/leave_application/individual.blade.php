@extends('layouts.app')
@section('title', '| Leave Application')
@section('header', "Leave Application ( $employee->name )")
@section('content')
    @push('css')
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
                                    <li class="breadcrumb-item" aria-current="page">Leave Application</li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $employee->name }}</li>
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
                                                    @foreach ($department->employees as $row)
                                                        <option value="{{ $row->id }}"
                                                            {{ request()->get('employee_id') == $row->id ? 'selected' : '' }}>
                                                            {{ $row->name }}
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
                                <h5 class="card-title">Leave Application List</h5>
                            </div>
                            <table class="table table-hover table-responsive table-sm table-bordered w-25">
                                <tbody>
                                    <tr>
                                        <td width="40%">Joining Date: </td>
                                        <td width="60%">{{ format_date_two($employee->join_date) }}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%">Left Date: </td>
                                        <td width="60%">{{ format_date_two($employee->left_date) }}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%">Leave Table : </td>
                                        <td width="60%">{{ $employee->leaveTable?->title }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card table-v2 table-responsive">
                            <table id="" class="table table-hover  table-sm table-bordered">
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
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="13">No Data Found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--New leave Application Modal -->
        <div class="modal fade" id="leaveApplicationModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xxl modal-dialog-scrollable">
                <div class="modal-content overflow-visible">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            Add Leave Application {{ $employee->name }}
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body overflow-x-hidden" id="leave_application_section">

                    </div>
                </div>
            </div>
        </div>
        <!-- / Modal -->

        <!--Edit leave Modal -->
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
            $(document).ready(function() {
                $("#leave_employee , #leave_type_id , #status ,#email_to").select2({
                    dropdownParent: $('#leaveApplicationModal'),
                    tags: true,
                    tokenSeparators: [','],
                    width: '100%'
                });
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
            $(document).ready(function() {
                $('#searchForm').on('change', function() {
                    $('#searchForm').submit(); // Submit the form
                });
            });
        </script>
    @endpush
@endsection
