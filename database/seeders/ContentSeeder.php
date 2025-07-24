<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Content;
use App\Models\User;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Certifique-se de ter pelo menos 1 usuário na base
        $userIds = User::pluck('id')->toArray();

        for ($i = 1; $i <= 30; $i++) {
            $package = Package::create([
                'title' => 'Package ' . $i,
                'code' => 'PKG' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'description' => fake()->sentence(10),
                'tags' => [fake()->word(), fake()->word()],
                'image' => 'packages/sample.jpg',
                'status' => ['rascunho', 'publicado', 'pendente', 'arquivado'][array_rand(['rascunho', 'publicado', 'pendente', 'arquivado'])],
                'type' => ['aula', 'tipo2', 'tipo3', 'outro'][array_rand(['aula', 'tipo2', 'tipo3', 'outro'])],
                'project_id' => 1, // ajuste conforme necessário
            ]);

            $contentsCount = rand(10, 20);
            for ($j = 1; $j <= $contentsCount; $j++) {
                Content::create([
                    'package_id' => $package->id,
                    'file' => 'contents/sample' . rand(1, 5) . '.jpg',
                    'title' => 'Content ' . $j . ' of Package ' . $i,
                    'author_id' => $userIds[array_rand($userIds)],
                    'ownership_rights' => 'Copyright (todos os direitos reservados)',
                    'source_credit' => fake()->name(),
                    'license_type' => 'CCC-9878',
                    'tags' => json_encode([fake()->word(), fake()->word()]),
                    'status' => [
                        'disponivel',
                        'pendente',
                        'em_fila',
                        'falhou',
                        'processando',
                        'temporariamente_indisponivel',
                        'aguardando_revisao',
                        'descarte'
                    ][array_rand([
                        'disponivel',
                        'pendente',
                        'em_fila',
                        'falhou',
                        'processando',
                        'temporariamente_indisponivel',
                        'aguardando_revisao',
                        'descarte'
                    ])],
                ]);
            }
        }
    }
}
