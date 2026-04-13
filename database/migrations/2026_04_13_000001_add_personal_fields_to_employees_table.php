<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('full_name');
            $table->string('birth_place')->nullable()->after('gender');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->text('full_address')->nullable()->after('phone');
            $table->string('identity_number')->nullable()->after('full_address');
            $table->string('marital_status')->nullable()->after('identity_number');
            $table->string('nationality')->nullable()->after('marital_status');
            $table->string('religion')->nullable()->after('nationality');
            $table->string('photo_path')->nullable()->after('religion');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'birth_place',
                'birth_date',
                'full_address',
                'identity_number',
                'marital_status',
                'nationality',
                'religion',
                'photo_path',
            ]);
        });
    }
};
