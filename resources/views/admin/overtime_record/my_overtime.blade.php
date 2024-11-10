@php
    $overtime_formats = Addons\Employee\Models\Overtime::get(['id', 'format']);
    $users = App\Models\User::get();
@endphp

<!--Create Modal -->
<div class="modal fade px-5" id="createOvertimeModalHome" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">
                    Add Overtime
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('employee.overtime.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="employee_id" value="{{ authEmployeeId() }}" hidden>
                    <div class="mb-3 col-md-12">
                        <label for="hours_dates" class="form-label">OT Pay</label>
                        <select name="ot_format" id="ot_format_home" class="form-control ot_format_home">
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
                        <input type="number" name="hours_dates" value="{{ old('hours_dates') }}" id=""
                            class="form-control number-input form-control-sm" required>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input class="datepicker-here form-control" placeholder="Select date" name="date"
                            data-range="false" data-multiple-dates-separator=" - " data-language="en"
                            data-format="dd-mm-yyyy" readonly data-position="bottom left" autocomplete="off"
                            value="{{ old('date') }}" required />
                    </div>

                    <div class="form-group mb-3">
                        <label for="month" class="form-label">Month to Apply</label>
                        <input type="text" id="month" class="form-control month" placeholder="Select Month"
                            name="month" value="{{ old('month') }}" readonly autocomplete="off" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="description" class="form-label">Remark</label>
                        <textarea class="form-control summernote" name="remark" id="" cols="10" rows="3">{{ old('remark') }}</textarea>
                    </div>

                    <input type="hidden" name="status" value="Pending" hidden>

                    @php
                        $firstTypeOneSelected = false;
                    @endphp
                    <div class="col-lg-12 email_to_wrap">
                        <div class="mb-3 d-flex flex-column">
                            <label for="email_to" class="form-label">Email To</label>
                            <select class="from-select w-100 email_to_home" name="email_to[]" id="email_to_home"
                                multiple="multiple" required>
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

@push('js')
    <script>
        $(document).ready(function() {
            $("#ot_format_home ,#email_to_home").select2({
                dropdownParent: $('#createOvertimeModalHome'),
                tags: true,
                tokenSeparators: [','],
                width: '100%'
            });
        });
    </script>
@endpush
