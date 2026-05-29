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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('task_number')->unique()->after('id')->nullable();
        });

        // For existing rows, assign a placeholder or generate via loop if many
        $tasks = \App\Models\Task::all();
        $counter = 1;
        foreach ($tasks as $task) {
            $task->task_number = 'TSK-' . str_pad($counter++, 4, '0', STR_PAD_LEFT);
            $task->save();
        }

        // Now make it non-nullable if we want
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('task_number')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('task_number');
        });
    }
};
