<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create( 'leave_applications', function ( Blueprint $table ) {
            $table->id();
            $table->unsignedBigInteger( 'employee_id' );
            $table->unsignedBigInteger( 'auth_id' );
            $table->unsignedBigInteger( 'leave_type_id' );
            $table->unsignedBigInteger( 'leave_table_id' );
            $table->string( 'month_to_pay' )->nullable();
            $table->date( 'date_from' );
            $table->date( 'date_to' );
            $table->integer( 'number_of_days' );
            $table->integer( 'balance' );
            $table->text( 'remarks' )->nullable();
            $table->string( 'status' )->nullable();
            $table->string( 'email_to' )->nullable();
            $table->string( 'attachment' )->nullable();
            $table->string( 'attachment_two' )->nullable();
            $table->foreign( 'employee_id' )->references( 'id' )->on( 'employees' )->onDelete( 'cascade' );
            $table->foreign( 'auth_id' )->references( 'id' )->on( 'users' )->onDelete( 'cascade' );
            $table->foreign( 'leave_type_id' )->references( 'id' )->on( 'leave_types' )->onDelete( 'cascade' );
            $table->foreign( 'leave_table_id' )->references( 'id' )->on( 'leave_tables' )->onDelete( 'cascade' );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists( 'leave_applications' );
    }
};
