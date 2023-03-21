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
        Schema::create('receipt_details', function (Blueprint $table) {
            $table->id();
            $table->integer('order_no')->nullable();
            $table->unsignedBigInteger('receipt_id')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('qty')->nullable();
            $table->double('price')->default(0);
            $table->double('total')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('receipt_id')->references('id')
            ->on('receipts')
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
        Schema::dropIfExists('receipt_details');
    }
};
