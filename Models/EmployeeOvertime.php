<?php

namespace Addons\Attendance\Models;

use Addons\Employee\Models\Employee;
use Addons\Employee\Models\Overtime;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOvertime extends Model {
    use HasFactory;

    public function user() {
        return $this->belongsTo( User::class, 'auth_id' );
    }

    public function employee() {
        return $this->belongsTo( Employee::class );
    }

    public function overtime() {
        return $this->belongsTo( Overtime::class );
    }
}
