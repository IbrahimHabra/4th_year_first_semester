<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('modify_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price_default', 10, 2);
            $table->integer('amount')->default(1);
            $table->integer('warning_amount')->default(0);
            $table->timestamps();
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->date('start_date');
            $table->date('exit_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
        Schema::dropIfExists('parts');
        Schema::dropIfExists('modify_types');
        Schema::dropIfExists('companies');
    }
};