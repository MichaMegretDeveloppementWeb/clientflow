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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('general');
            $table->enum('event_type', ['step', 'billing'])->default('step');
            $table->string('status', 50)->default('todo');
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('payment_status', ['paid', 'pending'])->nullable();
            $table->timestamp('created_date')->nullable();
            $table->datetime('execution_date')->nullable()->comment("Date d'exécution pour les étapes");
            $table->datetime('send_date')->nullable()->comment("Date d'envoi pour les facturations");
            $table->datetime('payment_due_date')->nullable()->comment("Date d'échéance de paiement pour les facturations");
            $table->timestamp('paid_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
            
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
