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
            $table->string('token')->nullable();
            // $table->renameColumn('token', 'api_token');

            // $table->text('text')->change();
            // mengganti tipe data
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
          $table->dropColumn('token');
        //   $table->renameColumn('api_token', 'token');
        // mengganti nama kolom

        $table->string('token')->change();
        });
    }
};
