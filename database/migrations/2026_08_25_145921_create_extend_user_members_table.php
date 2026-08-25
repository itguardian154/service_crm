<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('extend_user_members', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_member_id');

            $table->unsignedInteger('duration_month');

            $table->unsignedBigInteger('amount');

            $table->date('extended_from');

            $table->date('extended_until');

            $table->string('status')->default('success');

            $table->timestamps();

            $table->foreign('user_member_id')
                ->references('id')
                ->on('users_member')
                ->cascadeOnDelete();

            $table->index('user_member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extend_user_members');
    }
};
