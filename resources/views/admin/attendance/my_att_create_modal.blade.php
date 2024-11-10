<!--Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">
                    Mark attendance
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                @if (config('employee.enabled'))
                    <form action="{{ route('attendance.check-in-check-out') }}" method="POST">
                        @csrf
                        <div class="mb-3 d-flex flex-column justify-content-center align-items-center">
                            <p for="date" class="col-form-label">
                                Please click "Check In" button to continue
                            </p>
                            @if (function_exists('employeePresent'))
                                @if (employeePresent() == true)
                                    <button type="submit" class="btn btn-danger"> Check Out</button>
                                @else
                                    <button type="submit" class="btn btn-success" value="Check In">Check
                                        In</button>
                                @endif
                            @endif
                        </div>
                    </form>
                @else
                    <h4 for="date" class="col-form-label text-center text-danger">
                        <i class="fa fa-exclamation-triangle fs-1"></i><br>
                        This feature is not available yet
                    </h4>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->
