<?php

use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();

            $table->foreignId('user_id')->constrained((new User())->getTable())->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled']);
            $table->text('shipping_address');
            $table->string('payment_method');
            $table->enum('payment_status', [
                'pending', //When payment is initiated but not yet confirmed (e.g., awaiting bank transfer)
                'paid', //Successfully completed payment
                'unpaid', //No payment received or payment cancelled
                'failed', //Payment attempt made but failed (e.g., declined card)
            ]);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
