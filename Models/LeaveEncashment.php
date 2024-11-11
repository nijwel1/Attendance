<?php

namespace Addons\Attendance\Models;

use Addons\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveEncashment extends Model {
    use HasFactory;

    public function employee() {
        return $this->belongsTo( Employee::class );
    }
}
