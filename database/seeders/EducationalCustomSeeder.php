<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EducationalCustomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. School Years de 2000 até o ano atual
        $currentYear = (int)date('Y');
        for ($year = 2000; $year <= $currentYear; $year++) {
            DB::table('school_years')->insert([
                'year' => (string)$year,
                'current' => $year === $currentYear,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Subjects - 20 componentes educacionais
        $subjectNames = [
            'Matemática',
            'Português',
            'História',
            'Geografia',
            'Ciências',
            'Inglês',
            'Artes',
            'Educação Física',
            'Biologia',
            'Física',
            'Química',
            'Filosofia',
            'Sociologia',
            'Literatura',
            'Redação',
            'Espanhol',
            'Tecnologia',
            'Robótica',
            'Empreendedorismo',
            'Educação Financeira'
        ];
        foreach ($subjectNames as $i => $name) {
            DB::table('subjects')->insert([
                'title' => $name,
                'code' => 'SUBJ' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'description' => "Componente curricular de $name.",
                'status' => 'publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Course Categories - 10 categorias de cursos educacionais
        $categoryNames = [
            'Ensino Fundamental',
            'Ensino Médio',
            'Técnico',
            'Profissionalizante',
            'Idiomas',
            'Artes',
            'Exatas',
            'Humanas',
            'Biológicas',
            'Tecnologia'
        ];
        foreach ($categoryNames as $i => $name) {
            DB::table('course_categories')->insert([
                'title' => $name,
                'code' => 'CAT' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'description' => "Categoria de curso: $name.",
                'status' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Projects - 10 projetos educacionais
        for ($i = 1; $i <= 10; $i++) {
            DB::table('projects')->insert([
                'title' => "Projeto Educacional $i",
                'image' => null,
                'summary' => "Resumo do projeto educacional $i.",
                'status' => 'publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Courses - 30 cursos educacionais
        $categoryIds = DB::table('course_categories')->pluck('id')->toArray();
        $projectIds = DB::table('projects')->pluck('id')->toArray();
        for ($i = 1; $i <= 30; $i++) {
            DB::table('courses')->insert([
                'title' => "Curso Educacional $i",
                'image' => null,
                'summary' => "Resumo do curso educacional $i.",
                'description' => "Descrição detalhada do curso educacional $i.",
                'status' => 'publicado',
                'is_featured' => false,
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'project_id' => $projectIds[array_rand($projectIds)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. Series - do 1º ano do fundamental ao 3º ano do médio
        $seriesNames = [
            '1º Ano Fundamental',
            '2º Ano Fundamental',
            '3º Ano Fundamental',
            '4º Ano Fundamental',
            '5º Ano Fundamental',
            '6º Ano Fundamental',
            '7º Ano Fundamental',
            '8º Ano Fundamental',
            '9º Ano Fundamental',
            '1º Ano Médio',
            '2º Ano Médio',
            '3º Ano Médio'
        ];
        $courseIds = DB::table('courses')->pluck('id')->toArray();
        $subjectIds = DB::table('subjects')->pluck('id')->toArray();

        foreach ($seriesNames as $i => $name) {
            $seriesId = DB::table('series')->insertGetId([
                'course_id' => $courseIds[array_rand($courseIds)],
                'title' => $name,
                'description' => "Descrição da série $name.",
                'status' => 'publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // Vincula de 3 a 6 subjects aleatórios para cada série
            $subjectsForSeries = collect($subjectIds)->shuffle()->take(rand(3, 6))->toArray();
            foreach ($subjectsForSeries as $subjectId) {
                DB::table('series_subject')->insert([
                    'series_id' => $seriesId,
                    'subject_id' => $subjectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
