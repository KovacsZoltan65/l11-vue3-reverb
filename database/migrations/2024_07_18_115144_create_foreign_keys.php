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
        try{
            Schema::table('projects', function (Blueprint $table) {
                $table->foreign('id')
                        ->references('projectId')
                        ->on('task_progress')
                        ->onDelete('cascade');            
            });
        }catch(\Exception $e){
            \Log::info($e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foreign_keys');
    }
};
