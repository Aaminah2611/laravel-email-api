<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->softDeletes(); // adds deleted_at column
    });

    Schema::table('email_templates', function (Blueprint $table) {
        $table->softDeletes();
    });

    Schema::table('emails', function (Blueprint $table) {
        $table->softDeletes();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });

    Schema::table('email_templates', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });

    Schema::table('emails', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });
}

};
