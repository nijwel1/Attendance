<form action="{{ route('employee.overtime.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" value="{{ $data->id }}" name="id">
    <div class="mb-3">
        <label for="employee_edit_id" class="form-label">Employee<sup class="text-danger">*</sup></label>
        <select name="employee_id" class="form-control employee_edit_id" id="employee_edit_id" required>
            <option value="" selected disabled>Select Employee</option>
            @foreach ($departments as $department)
                @if ($department->employees->isNotEmpty())
                    <optgroup label="{{ $department?->name }}">
                        @foreach ($department->employees as $employee)
                            <option value="{{ $employee->id }}"
                                {{ $data->employee_id == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}</option>
                        @endforeach
                    </optgroup>
                @endif
            @endforeach
        </select>
    </div>

    <div class="mb-3 col-md-12">
        <label for="overtime_format_edit" class="form-label">Overtime Pay<sup class="text-danger">*</sup></label>
        <select name="ot_format" id="overtime_format_edit" class="form-control overtime_format_edit">
            <option value="" selected disabled>Select overtime pay format</option>
            @foreach ($overtime_formats as $format)
                <option value="{{ $format->id }}" {{ $data->overtime_id == $format->id ? 'selected' : '' }}>
                    {{ $format->format }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3 col-md-12">
        <label for="amount_capping" class="form-label">No of Hours/Dates<sup class="text-danger">*</sup></label>
        <input type="number" name="hours_dates" id="edit_hours_dates"
            value="{{ old('hours_dates', $data->hours_dates) }}" id=""
            class="form-control number-input form-control-sm" required>
    </div>

    <div class="mb-3">
        <label for="date" class="form-label">Date<sup class="text-danger">*</sup></label>
        <input class="datepicker-edit-here form-control" placeholder="Select date" name="date" id="edit_date"
            readonly autocomplete="off" value="{{ $data->date ? datepicker_format($data->date) : '' }}" required />
    </div>

    <div class="form-group mb-3">
        <label for="month" class="form-label">Month to Return<sup class="text-danger">*</sup></label>
        <input type="text" id="edit_month" class="form-control month" placeholder="Select Month" name="month"
            value="{{ old('month', $data->month) }}" readonly autocomplete="off" required />
    </div>

    <div class="mb-3 col-md-12">
        <label for="description" class="form-label">Remark</label>
        <textarea class="form-control summernote" name="remark" id="edit_remark" cols="10" rows="3">{{ old('remark', $data->remarks) }}</textarea>
    </div>

    <div class="form-group mb-3">
        <label for="status" class="form-label">Status<sup class="text-danger">*</sup></label>
        <select name="status" class="form-control status" id="edit_status" required>
            <option value="Pending" {{ $data->status == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Canceled" {{ $data->status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
            <option value="Rejected" {{ $data->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="Approved" {{ $data->status == 'Approved' ? 'selected' : '' }}>Approved</option>
        </select>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.summernote').each(function() {
            $(this).summernote({
                height: 200,
            });
        })
    });

    $(document).ready(function() {
        $("#employee_edit_id ,#overtime_format_edit ,#edit_status").select2({
            dropdownParent: $('#overtimeEditModal'),
            tags: true,
            tokenSeparators: [','],
            width: '100%'
        });
    });

    //month picker
    $(document).ready(function() {
        $('.month').datepicker({
            format: "mm/yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Initialize the datepicker
        $('.datepicker-edit-here').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2020,
            maxYear: moment().year(),
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'DD-MM-YYYY',
                applyLabel: 'ok',
            }
        });

        // Apply the selected date to the input field
        $(".datepicker-edit-here").on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        // Clear the date when canceled
        $(".datepicker-edit-here").on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
</script>
