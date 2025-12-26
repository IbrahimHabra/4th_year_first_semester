<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('record_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('parts')->cascadeOnDelete();
            $table->foreignId('modify_type_id')->constrained('modify_types')->cascadeOnDelete();
            $table->timestamp('modification_date');
            $table->integer('modified_amount');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('models')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('customer_name');
            $table->timestamp('add_date')->useCurrent();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('finish_date')->nullable();
            $table->decimal('on_hand_price', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('part_id')->constrained('parts')->cascadeOnDelete();
            $table->decimal('part_price', 10, 2);
            $table->integer('part_amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('record_parts');
        Schema::dropIfExists('models');
    }
};