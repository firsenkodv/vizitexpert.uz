<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Единые реквизиты Визит Эксперт в «Настройках сайта → Контакты».
 *
 * Юрлицо одно на все три сайта группы, а у клонов в настройках оставались
 * свои: на vizitexpert.kg — бишкекский адрес и ОсОО.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('moonshine_settings')->update([
            'fullAddress' => 'Инд: 050000, Республика Казахстан, г. Алматы пр. Сейфуллина 498 оф. 202',
            'address' => 'Республика Казахстан, г. Алматы пр. Сейфуллина 498 оф. 202',
            'country' => 'Республика Казахстан',
            'sityAddress' => 'г. Алматы пр. Сейфуллина 498 оф. 202',
            'idn' => 'Инд: 05000',
            'bin' => 'БИН 071140001056',
            'company_name' => 'ТОО "Vizit Expert" (Визит Эксперт)',
        ]);
    }

    public function down(): void
    {
        // Прежние значения у каждого сайта были свои, восстанавливать нечего.
    }
};
