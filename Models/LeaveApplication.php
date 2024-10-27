<?php

namespace Addons\Attendance\Models;

use Addons\Employee\Models\Employee;
use Addons\Employee\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model {
    use HasFactory;

    protected $fillable = [];

    public function leaveType() {
        return $this->belongsTo( LeaveType::class );
    }

    public function employee() {
        return $this->belongsTo( Employee::class );
    }

}
