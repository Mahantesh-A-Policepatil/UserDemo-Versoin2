<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name')->unique();
            $table->string('group_desc')->nullable();
            $table->unsignedBigInteger('group_member_id');
            $table->foreign('group_member_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_groups');
    }
}
