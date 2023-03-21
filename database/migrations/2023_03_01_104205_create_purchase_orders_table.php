<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('purchase_name');
            $table->datetime('date')->nullable();
            $table->string('note')->nullable();
            $table->double('sub_total')->default(0);
            $table->double('discount')->default(0);
            $table->double('tax')->default(0);
            $table->double('total')->default(0);
            $table->unsignedBigInteger('creared_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('invoice_id')->references('id')
            ->on('invoices')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreign('company_id')->references('id')
            ->on('companies')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreign('currency_id')->references('id')
            ->on('currencies')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreign('creared_by')->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
};
