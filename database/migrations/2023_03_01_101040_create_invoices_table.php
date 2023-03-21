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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('invoice_name');
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->string('note')->nullable();
            $table->double('sub_total')->default(0);
            $table->double('discount')->default(0);
            $table->double('tax')->default(0);
            $table->double('total')->default(0);
            $table->enum('status', ['created', 'pending', 'paid', 'canceled'])->default('pending');
            $table->unsignedBigInteger('creared_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('invoices');
    }
};
