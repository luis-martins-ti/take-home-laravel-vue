<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamRequestItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('exam_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->enum('laterality', ['OD', 'OE', 'AO'])->nullable();
            $table->string('comment')->nullable();
            $table->enum('group', ['Individual', 'Grupo 1', 'Grupo 2', 'Grupo 3', 'Grupo 4', 'Grupo 5']);
            $table->foreignId('package_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_request_items');
    }
}
