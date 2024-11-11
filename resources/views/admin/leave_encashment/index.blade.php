@extends('layouts.app')
@section('title', '|Leave Encashment')
@section('header', 'Leave Encashment')
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
                    <div class="col-lg-2">
                        <div class="breadcrumb-wrapper">
                            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
                                aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Leave Encashment</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <form action="{{ route('leave.encashment.index') }}" method="GET" id="searchForm">
                            <div class="row">
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

                    <div class="col-lg-4">
                        <div class="">
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" id="deleteAllBtn" disabled class="btn btn-sm btn-danger me-2"> <i
                                        class="far fa-trash-alt"></i> All
                                    Delete
                                </button>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#leaveApplicationModal"
                                    class="btn btn-base btn-sm add-leave-application" data-id="{{ $employee?->id }}">
                                    New Leave Encashment
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
                        <div class="card table-responsive table-v2">
                            <div class="card-header mb-4 p-2">
                                <h5 class="card-title">Leave Encashment List</h5>
                            </div>

                            <form id="deleteAllForm" action="{{ route('leave.encashment.delete.all') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <table class="table table-hover text-center">
                                    <thead>
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="checkAll"> #
                                            </th>
                                            <th width="15%">Employee</th>
                                            <th width="10%">Encashment Day</th>
                                            <th width="10%">Encashment Amount</th>
                                            <th width="10%">Month to Apply</th>
                                            <th width="10%">Remarks</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Attachment</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($leave_encashments as $key => $leave)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="id[]" value="{{ $leave->id }}">
                                                    {{ $key + 1 }}
                                                </td>
                                                <td>{{ $leave->employee?->name }} <br> ID:
                                                    {{ $leave->employee?->employee_id }}</td>
                                                <td>{{ $leave?->encashment_day }}</td>
                                                <td>{{ $leave?->encashment_amount }}</td>
                                                <td>{{ $leave?->month_to_apply }}</td>
                                                <td>{{ $leave?->remarks }}</td>
                                                <td>{{ $leave?->status }}</td>
                                                <td class="text-center">
                                                    @if ($leave?->attachment)
                                                        <a href="{{ asset($leave?->attachment) }}" target="_blank">
                                                            <button type="button" class="btn btn-outline-base btn-sm edit">
                                                                <i class="fa fa-download" aria-hidden="true"></i>
                                                            </button>
                                                        </a>
                                                    @else
                                                        <span>No Attachment</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <button type="button" class="btn btn-outline-base btn-sm edit"
                                                            data-id="{{ $leave->id }}" data-bs-toggle="modal"
                                                            data-bs-target="#editModal">
                                                            <i class="far fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('leave.encashment.destroy', $leave->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-outline-danger btn-sm delete">
                                                                <i class="far fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center" colspan="9">No Data Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </form>

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
                            New Leave Encashment
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
        >

        <script>
            $('body').on('click', '.add-leave-application', function test() {
                var id = $(this).data('id');
                var date = $(this).data('date');
                $.ajax({
                    url: "{{ url('admin/leave-encashment/create') }}",
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


            $('body').on('click', '.edit', function() {
                var id = $(this).data('id');
                $.get("{{ url('admin/leave-encashment/edit') }}/" + id,
                    function(data) {
                        console.log(data);

                        $('#edit_section').html(data);
                    })
            });
        </script>

        <!-- select 2  -->
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

        <!-- fetch employee data  -->
        <script>
            $('body').on('change', '.employee_id', function() {

                var id = $(this).val();

                $.ajax({
                    url: "{{ url('admin/leave-encashment/create') }}",
                    type: 'GET',
                    data: {
                        id: id,
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

        <!-- search form  -->
        <script>
            $(document).ready(function() {
                $('#year').on('change', function() {
                    $('#searchForm').submit(); // Submit the form
                });
            });
        </script>

        <!-- delete All  -->
        <script>
            $(document).ready(function() {
                const $deleteAllBtn = $('#deleteAllBtn');
                const $checkboxes = $('input[name="id[]"]');
                const $checkAll = $('#checkAll');

                // Function to update the delete button state
                function updateDeleteButtonState() {
                    $deleteAllBtn.prop('disabled', !$checkboxes.is(':checked'));
                }

                // Event listener for individual checkboxes
                $checkboxes.change(function() {
                    updateDeleteButtonState();
                });

                // Event listener for the "Check All" checkbox
                $checkAll.change(function() {
                    $checkboxes.prop('checked', this.checked);
                    updateDeleteButtonState();
                });

                // Initial state check
                updateDeleteButtonState();
            });

            $('body').on('click', '#deleteAllBtn', function() {
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
                        $('#deleteAllForm').submit();
                    }
                })
            });
        </script>
    @endpush
@endsection
