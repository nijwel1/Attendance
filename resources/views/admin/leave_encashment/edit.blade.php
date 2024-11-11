
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<form action="{{ route('leave.encashment.update', $enceshment->id) }}" method="POST" enctype="multipart/form-data">@csrf

    <div class="row" id="leave_application" >
        <div class="form-group mb-3 col-lg-6">
            <div class="form-group mb-3 row">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">Employee</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9"><select class="form-select select2 form-control-sm employee_id"
                        id="edit_leave_employee" name="employee_id" required style="width: 100%">
                        @foreach ($departments as $department)
                            <optgroup label="{{ $department?->name }}">
                                @foreach ($department->employees as $row)
                                    <option value="{{ $row->id }}"
                                        {{ $enceshment->employee_id == $row->id ? 'selected' : '' }}>{{ $row->name }} </option>
                                @endforeach
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group mb-3 row">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">Encashment Days</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <input type="number" name="encashment_day" id="encashment_day" class="form-control form-control-sm" value="{{ old('encashment_day', $enceshment->encashment_day) }}" required>
                </div>
            </div>
            <div class="form-group mb-3 row">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">Encashment Amount</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <input type="number" name="encashment_amount" id="encashment_amount" class="form-control form-control-sm" value="{{ old('encashment_amount', $enceshment->encashment_amount) }}" required>
                   <small>(Monthly Gross rate of pay** x 12 months) / (52 weeks x number of working days each week) x No. of leave days left to encash</small>
                </div>
            </div>
            <div class="form-group mb-3 row">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">Month to Apply</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <input type="text" id="month_to_apply" class="form-control month input-sm"
                        placeholder="Month to Apply" name="month_to_apply"
                        value="{{ isset($month) ? $month : old('month', $enceshment->month_to_apply) }}" readonly autocomplete="off"
                        required />

                    <input type="text" name="leave_table_id" value="{{ $employee->leaveTable?->id }}"
                        id="leave_table_id" hidden>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-lg-3"><label for="input4" class="form-label">Remarks</label></div>
                <div class="col-lg-9">
                    <textarea type="text" name="remarks" rows="5" class="form-control" id="input3" placeholder="Remarks">{{ old('remarks', $enceshment->remarks) }}</textarea>
                </div>
            </div>
            <div class="form-group mb-3 row leave_type_id_wrap">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">Status</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <select class="form-select select2 form-control-sm select2" id="status" name="status" required
                        style="width: 100%">
                        <option value="Pending" {{ $enceshment->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Canceled" {{ $enceshment->status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                        <option value="Approved" {{ $enceshment->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ $enceshment->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-lg-3"><label for="input4" class="form-label">File</label></div>
                <div class="col-lg-9">
                    <input type="file" name="attachment" id="attachment_one"
                        class="form-control form-control-sm mb-1" />
                </div>
            </div>
            <div class="form-group mb-3 row email_to_wrap">
                <div class="col-lg-3"><label class="mb-2 form--label text--white">Email To</label><span
                        class="text-danger">*</span></div>
                <div class="col-lg-9">
                    <select class="from-select w-100 select2" name="email_to[]" id="edit_mail_to" multiple="multiple"
                        required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, $email_to) ? 'selected' : '' }}>
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
                            <tr>
                                <th>Leave Type</th>
                                <td>Annual</td>
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
                <div class="encashment_leave_div">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0"
                        class="table table-bordered table-sm table-responsive leave-table ">
                        <tbody>
                            <tr>
                                <th>Encashment Days</th>
                                <th>Encashment Amount</th>
                                <th>Month to Apply</th>
                                <th>Status</th>
                            </tr>
                            @if ($leave_encashments)
                                @foreach ($leave_encashments as $leaveEncashmentDetail)
                                    <tr>
                                        <td>{{ $leaveEncashmentDetail->encashment_day }}</td>
                                        <td>{{ $leaveEncashmentDetail->encashment_amount }}</td>
                                        <td>{{ $leaveEncashmentDetail->month_to_apply }}</td>
                                        <td>{{ $leaveEncashmentDetail->status }}</td>
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
            </fieldset>
        </div>

        <div class="modal-footer"><button type="submit" class="btn btn-primary">Submit</button></div>
    </div>
</form>

 <!-- bootstrap monthpicker -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>


<script>
    $(document).ready(function() {
        $("#edit_leave_employee,#edit_mail_to").select2({
            dropdownParent: $('#editModal'),
            tags: true,
            tokenSeparators: [','],
            width: '100%'
        });
    });
</script>

<!-- month picker -->
<script>
    $(document).ready(function() {
        var currentDate = new Date();
        var currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0');
        var currentYear = currentDate.getFullYear();
        $('#month_to_apply').val(currentMonth + '/' + currentYear);

        $('.month').datepicker({
            format: "mm/yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true
        });
    });
</script>

