<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTimestampsAddUnixTimestampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasColumn('users', 'mobile')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('mobile');
            });
        }

        if (Schema::hasColumn('users', 'created_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('created_at');
            });
        }

        if (Schema::hasColumn('users', 'updated_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }

        if (Schema::hasColumn('groups', 'created_at')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('created_at');
            });
        }

        if (Schema::hasColumn('groups', 'updated_at')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }

        if (Schema::hasColumn('group_users', 'created_at')) {
            Schema::table('group_users', function (Blueprint $table) {
                $table->dropColumn('created_at');
            });
        }

        if (Schema::hasColumn('group_users', 'updated_at')) {
            Schema::table('group_users', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }

        Schema::table('users', function ($table) {
            $table->bigInteger('mobile');
            $table->integer('created_at');
            $table->integer('updated_at');
        });

        Schema::table('groups', function ($table) {
            $table->integer('created_at');
            $table->integer('updated_at');
        });

        Schema::table('group_users', function ($table) {
            $table->integer('created_at');
            $table->integer('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
