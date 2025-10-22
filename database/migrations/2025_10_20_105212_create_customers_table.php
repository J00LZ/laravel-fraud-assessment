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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('customerId');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('ip');
            $table->string('iban');
            $table->string('phoneNumber');
            $table->date('dateOfBirth');
            $table->boolean("valid")->default(true);
            $table->foreignIdFor(\App\Models\Scan::class)->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
