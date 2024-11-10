<?php

namespace Addons\Attendance\Models;

use Addons\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model {
    use HasFactory;

    protected $fillable = [];

    public function employee() {
        return $this->belongsTo( Employee::class )->with( 'WorkTable' );
    }

}
