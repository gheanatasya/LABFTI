<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->uuid('tokenable_id')->nullable()->change(); // Change the data type to UUID
        });

        // Update existing records to use UUID
        DB::statement('UPDATE personal_access_tokens SET tokenable_id = uuid_generate_v4()');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // You need to revert the changes manually
        // This down() method only drops the column, you need to define the appropriate logic based on your needs
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn('tokenable_id');
        });
    }

};
