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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string("name",100)->unique();
            $table->string("code",100)->unique();
            $table->boolean("status")->default(true);
            $table->timestamps();
        });
        
        Schema::create('sprints', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['abierto', 'cerrado','pendiente'])->default('pendiente');;
            $table->boolean("is_delete")->default(false);
            $table->timestamps();
            $table->foreignId("project_id")->constrained("projects")
            ->onUpdate("cascade")->onDelete("restrict");
        });

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->boolean("isFinally");
            $table->timestamps();
        });

        Schema::create('epics', function (Blueprint $table) {
            $table->id();
            $table->string("name",100);
            $table->string("description",100);
            $table->foreignId("state_id")->constrained("states")
            ->onUpdate("cascade")->onDelete("restrict");
            $table->timestamps();
            $table->foreignId("project_id")->constrained("projects")
            ->onUpdate("cascade")->onDelete("restrict");
        });

        Schema::create('user_stories', function (Blueprint $table) {
            $table->id();
            $table->string("name",100);
            $table->string("description",100);
            $table->integer("points");
            $table->foreignId("user_id")->constrained("users")
            ->onUpdate("cascade")->onDelete("restrict");
            $table->foreignId("epic_id")->constrained("epics")
            ->onUpdate("cascade")->onDelete("restrict");
            $table->foreignId("state_id")->constrained("states")
            ->onUpdate("cascade")->onDelete("restrict");
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string("title",100);
            $table->boolean("status");
            $table->foreignId("user_story_id")->constrained("user_stories")
            ->onUpdate("cascade")->onDelete("restrict");
            $table->timestamps();
        });

        Schema::create('assigned_users', function (Blueprint $table) {
            $table->id();
            $table->boolean("isAdmin");
            $table->foreignId("user_id")->constrained("users")
            ->onUpdate("cascade")->onDelete("restrict");
            $table->foreignId("project_id")->constrained("projects")
            ->onUpdate("cascade")->onDelete("restrict");
            $table->timestamps();
        });

        Schema::create('storiesx_sprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId("sprint_id")->constrained("sprints")
            ->onUpdate("cascade")->onDelete("restrict");
            $table->foreignId("story_id")->constrained("user_stories")
            ->onUpdate("cascade")->onDelete("restrict");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprints');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('states');
        Schema::dropIfExists('epics');
        Schema::dropIfExists('user_stories');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('assigned_users');
        Schema::dropIfExists('storiesx_sprints');
    }
};
