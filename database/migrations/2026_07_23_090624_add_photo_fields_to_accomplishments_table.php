<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accomplishments', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('description');
            $table->decimal('latitude', 10, 7)->nullable()->after('photo_path');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('address')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('accomplishments', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'latitude', 'longitude', 'address']);
        });
    }
};
