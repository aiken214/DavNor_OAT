<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employee')->after('tag');
            $table->foreignId('section_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->after('section_id')->constrained()->nullOnDelete();
            $table->foreignId('school_id')->nullable()->after('district_id')->constrained()->nullOnDelete();
        });

        DB::table('users')->where('is_admin', true)->update(['role' => 'super_admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('tag');
        });

        DB::table('users')->where('role', 'super_admin')->update(['is_admin' => true]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['school_id']);
            $table->dropColumn(['role', 'section_id', 'district_id', 'school_id']);
        });
    }
};
