<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Account\Models\TransactionType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('account_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('transfer_id')
                ->nullable()
                ->constrained('transfers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('type', array_column(TransactionType::cases(), 'value'))
                ->default(TransactionType::Deposit->value);
            $table->decimal('amount', 15, 2);
            $table->timestamp('transaction_date')->useCurrent();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::statement(<<<SQL
            ALTER TABLE transactions
            ADD CONSTRAINT check_transactions_amount CHECK (amount > 0)
        SQL
        );
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
