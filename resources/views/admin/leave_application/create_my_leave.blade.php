<form action="{{ route('leave_application.store') }}" method="POST">@csrf

    <div class="row" id="leave_application" enctype="multipart/form-data">
        <div class="form-group mb-3 col-lg-6">
            <div class="form-group mb-3 alert alert-danger col-lg-10">
                <p>Your balance is: 0</p>
            </div>
            <input type="hidden" name="employee_id" value="{{ authEmployeeId() }}" hidden>
            <div class="form-group mb-3 row">
                <div class="col-lg-3">
                    <label class="mb-2 form--label text--white">Month To Pay</label><span class="text-danger">*</span>
                </div>
                <div class="col-lg-9">
                    <input data-language="en" type="text" value="{{ date('m/Y') }}" name="month_to_pay"
                        id="month-picker" class="form-control form-control-sm month" required readonly />
                </div>
            </div>
            <div class="form-group mb-3 row">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">From</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <input type="text" name="date_from" id="my_from_date" value="{{ $dateFrom ?? date('d-m-Y') }}"
                        class="form-control form-control-sm datepicker-here" required readonly />
                </div>
            </div>
            <div class="form-group mb-3 row">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">To</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <input type="text" name="date_to" id="my_to_date" value="{{ $dateFrom ?? date('d-m-Y') }}"
                        class="form-control form-control-sm datepicker-here" required readonly />
                </div>
            </div>
            <div class="form-group mb-3 row">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">Number of Days</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <input type="text" name="number_of_days" value="1" readonly id="my_number_of_days"
                        class="form-control form-control-sm" required />

                    <input type="text" name="leave_table_id" value="{{ $employee->leaveTable?->id }}"
                        id="leave_table_id" hidden>
                </div>
            </div>
            <div class="form-group mb-3 row">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">Leave Type</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <select class="form-select select2 form-control-sm select2" id="leave_type_id" name="leave_type_id"
                        required style="width: 100%">
                        @foreach ($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}">{{ $leaveType->title }} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-lg-3"><label for="input4" class="form-label">Remarks</label></div>
                <div class="col-lg-9">
                    <textarea type="text" name="remarks" rows="5" class="form-control" id="input3" placeholder="Remarks">{{ old('remarks') }}</textarea>
                </div>
            </div>
            <div class="form-group mb-3 row leave_type_id_wrap">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">Status</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <select class="form-select select2 form-control-sm select2" id="status" name="status" required
                        style="width: 100%">
                        <option value="Pending">Pending</option>
                        <option value="Canceled">Canceled</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-lg-3"><label for="input4" class="form-label">File</label></div>
                <div class="col-lg-9">
                    <input type="file" name="attachment" id="attachment_one"
                        class="form-control form-control-sm mb-1" />
                    <input type="file" name="attachment_two" id="attachment_two"
                        class="form-control form-control-sm" />
                    <small>(i.e. Medical Leave Records)</small>
                </div>
            </div>
            <div class="form-group mb-3 row email_to_wrap">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">Email To</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <select class="from-select w-100 select2" name="email_to[]" id="email_to" multiple="multiple"
                        required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @if ($user->type == 'admin') selected @endif>
                                {{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group mb-3 col-lg-6">
            <fieldset>
                <legend>Info</legend>
                <div class="table-responsive">
                    <table class="table table-hover table-responsive table-bordered leave-table">
                        <tbody>
                            <tr>
                                <th>Join Date</th>
                                <td>{{ format_date_two($employee->joining_date) }}</td>
                            </tr>
                            <tr>
                                <th>Left Date</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>Leave Table</th>
                                <td>{{ $employee->leaveTable?->title }}</td>
                            </tr>
                            <tr>
                                <th>Taken</th>
                                <td>0</td>
                            </tr>
                            <tr>
                                <th>Carried Forward</th>
                                <td>0</td>
                            </tr>
                            <tr>
                                <th>Leave Entitlement</th>
                                <td>0</td>
                            </tr>
                            <tr>
                                <th>Pro Rated Entitlement This Calendar Year</th>
                                <td>0</td>
                            </tr>
                            <tr>
                                <th>Pro Rated Entitlement Till Today's Date</th>
                                <td>0</td>
                            </tr>
                            <tr>
                                <th>Balance Entitlement For This Calendar Year</th>
                                <td>0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </fieldset><br>
            <fieldset>
                <legend>Leave Application</legend>
                <div class="application_leave_div">
                    <table width="100%" class="table table-bordered table-sm table-responsive leave-table ">
                        <tbody>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Number of Days</th>
                                <th>Type</th>
                                <th>Month to Apply</th>
                                <th>Status</th>
                            </tr>

                            @if ($employee?->leaveApplication)
                                @foreach ($employee?->leaveApplication as $leaveApp)
                                    <tr>
                                        <td>{{ format_date_two($leaveApp->date_from) }}</td>
                                        <td>{{ format_date_two($leaveApp->date_to) }}</td>
                                        <td>{{ $leaveApp->number_of_days }}</td>
                                        <td>{{ $leaveApp->leaveType?->title }}</td>
                                        <td>{{ $leaveApp->month_to_pay }}</td>
                                        <td>{{ $leaveApp->status }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" style="text-align: center;">No details available.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </fieldset><br>
            <fieldset>
                <legend>Leave Table</legend>
                <div class="table_leave_div">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0"
                        class="table table-bordered table-sm table-responsive leave-table ">
                        <tbody>
                            <tr>
                                <th>Leave Type</th>
                                <th>Year From</th>
                                <th>Year To</th>
                                <th>Entitlement</th>
                                <th>Carried Forward</th>
                            </tr>
                            @if ($employee?->leaveTable?->leaveTableDetails)
                                @foreach ($employee->leaveTable->leaveTableDetails as $leaveTableDetail)
                                    <tr>
                                        <td>Annual</td>
                                        <td>{{ $leaveTableDetail->from }}</td>
                                        <td>{{ $leaveTableDetail->to }}</td>
                                        <td>{{ $leaveTableDetail->entitlement }}</td>
                                        <td>{{ $leaveTableDetail->carried_forward }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" style="text-align: center;">No details available.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </fieldset><br>
            <fieldset>
                <legend>Leave Encashment</legend>
                <div class="encashment_leave_div"></div>
            </fieldset>
        </div>

        <div class="modal-footer"><button type="submit" id="leaveSubmit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.datepicker-here').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2020,
            maxYear: moment().year(),
            autoUpdateInput: true,
            locale: {
                cancelLabel: 'Clear',
                format: 'DD-MM-YYYY',
                applyLabel: 'ok',
            }
        });
        $(".datepicker-here").on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });
        $(".datepicker-here").on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#leave_employee , #leave_type_id , #status ,#email_to").select2({
            dropdownParent: $('#myLeaveApplicationModal'),
            tags: true,
            tokenSeparators: [','],
            width: '100%'
        });
    });
</script>

<script>
    $(document).ready(function() {
        function parseDate(dateString) {
            const parts = dateString.split('-');
            return new Date(parts[2], parts[1] - 1, parts[0]); // year, month (0-indexed), day
        }

        function calculateDays() {
            const fromDate = $('#my_from_date').val();
            const toDate = $('#my_to_date').val();

            if (fromDate && toDate) {
                const start = parseDate(fromDate);
                const end = parseDate(toDate);
                const timeDiff = end - start;

                // Calculate days
                const dayCount = Math.ceil(timeDiff / (1000 * 3600 * 24)); // Convert milliseconds to days

                if (dayCount >= 0) {
                    $('#my_number_of_days').val(dayCount + 1); // Add one day
                    $("#leaveSubmit").removeAttr('disabled', true);
                } else {
                    $('#my_number_of_days').val('');
                    showToast('End date must be after start date', 'error', 2500)
                    $("#leaveSubmit").attr('disabled', true);
                }
            } else {
                $('#my_number_of_days').val(''); // Clear if either date is empty
            }
        }

        // Event listeners for date input changes
        $('#my_from_date, #my_to_date').on('change', calculateDays);
    });

    $(document).ready(function() {
        $('.month').datepicker({
            format: "mm/yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true
        });
    });
</script>
