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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->string('file');
            $table->string('title');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('ownership_rights')->default('Copyright (todos os direitos reservados)');
            $table->string('source_credit')->nullable();
            $table->string('license_type')->nullable();
            $table->json('tags')->nullable();
            $table->enum('status', [
                'disponivel',
                'pendente',
                'em_fila',
                'falhou',
                'processando',
                'temporariamente_indisponivel',
                'aguardando_revisao',
                'descarte',
            ])->default('pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
