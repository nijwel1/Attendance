<?php

namespace Addons\Attendance\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveApplicationMail extends Mailable {
    use Queueable, SerializesModels;

    public $data;

    public function __construct( $data ) {
        $this->data = $data;
    }

    public function build() {
        return $this->view( 'LeaveApplicationMail::leave_application' )
            ->with( ['application' => $this->data] );
    }
}
