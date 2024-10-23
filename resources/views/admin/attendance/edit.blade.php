<form action="{{ route('attendance.update', $data->id) }}" method="POST">
    @csrf
    <div class="form-group mb-3">
        <label class="mb-2 form--label text--white">Employee</label>
        <span class="text-danger">*</span>
        <div>
            <select class="form-select from-select2 form-control-sm" id="employee" name="employee_id" required
                style="width: 100%">
                @foreach ($departments as $department)
                    <optgroup label="{{ $department?->name }}">
                        @foreach ($department->employees as $employee)
                            <option value="{{ $employee->id }}" @if ($employee->id == $data->employee_id) selected @endif>
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
            <input type="text" name="date" id="edit_date" value="{{ $data->date }}"
                class="form-control form-control-sm" required />
        </div>
    </div>

    <div class="datepair row">
        <div class="form-group mb-3 col-lg-6">
            <label class="mb-2 form--label text--white" for="in_time">In Time</label>
            <div>
                <input type="text" name="in_time" id="in_time" value="{{ $data->in_time }}"
                    class="input time start ui-timepicker-input valid form-control form-control-sm" autocomplete="off"
                    required />
            </div>

        </div>

        <div class="form-group mb-3 col-lg-6">
            <label class="mb-2 form--label text--white">out_time</label>
            <input type="text" name="out_time" id="out_time" value="{{ $data->out_time }}"
                class="input time end ui-timepicker-input form-control form-control-sm" autocomplete="off" />
        </div>
    </div>

    <div class="datepair row">
        <div class="form-group mb-3 col-lg-6">
            <label class="mb-2 form--label text--white">Break Time start</label>
            <div>
                <input type="text" name="break_start_time" value="{{ $data->break_start_time }}"
                    id="break_start_time"
                    class="input time start ui-timepicker-input valid form-control form-control-sm"
                    autocomplete="off" />
            </div>

        </div>

        <div class="form-group mb-3 col-lg-6">
            <label class="mb-2 form--label text--white">Break Time end</label>
            <input type="text" name="break_end_time" value="{{ $data->break_end_time }}" id="break_end_time"
                class="input time end ui-timepicker-input form-control form-control-sm" autocomplete="off" />
        </div>
    </div>

    <div class="mb-3">
        <label for="input4" class="form-label">Remarks</label>
        <textarea type="text" name="remarks" rows="5" class="form-control" id="input3" placeholder="Remarks">{{ old('remarks', $data->remarks) }}</textarea>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>


<script>
    $(function() {
        $('input[name="date"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'), 10),
            locale: {
                format: 'YYYY-MM-DD' // Change this to your desired format
            }
        }, function(start, end, label) {
            var years = moment().diff(start, 'years');
            alert("You are " + years + " years old!");
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $(".from-select2").select2();
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
