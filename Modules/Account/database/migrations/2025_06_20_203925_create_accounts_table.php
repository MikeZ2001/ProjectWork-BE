<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Account\Models\AccountType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', array_column(AccountType::cases(), 'value'))
                ->default(AccountType::CHECKING->value);
            $table->decimal('balance', 15, 2)
                ->default(0.00);
            $table->char('currency', 3)
                ->default('EUR');
            $table->dateTime('open_date');
            $table->dateTime('close_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'closed'])->default('active');
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });

        DB::statement(<<<SQL
                ALTER TABLE accounts
                ADD CONSTRAINT check_accounts_close_status
                CHECK (close_date IS NULL OR status = 'closed')
                SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
