<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create( 'employee_attendances', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( 'employee_id' )->constrained();
            $table->integer( 'auth_id' )->nullable();
            $table->date( 'date' );
            $table->string( 'day' );
            $table->string( 'in_time' );
            $table->string( 'out_time' )->nullable();
            $table->string( 'break_start_time' )->nullable();
            $table->string( 'break_end_time' )->nullable();
            $table->string( 'working_hours' )->nullable();
            $table->string( 'break_hours' )->nullable();
            $table->string( 'normal_hours' )->nullable();
            $table->string( 'overtime_hours' )->nullable();
            $table->enum( 'status', ['present', 'absent', 'late', 'early'] )->nullable();
            $table->text( 'remarks' )->nullable();
            $table->string( 'checkin_latitude' )->nullable();
            $table->string( 'checkin_longitude' )->nullable();
            $table->string( 'checkout_latitude' )->nullable();
            $table->string( 'checkout_longitude' )->nullable();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists( 'employee_attendances' );
    }
};
