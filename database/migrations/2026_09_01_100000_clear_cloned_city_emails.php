<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Чистка почты в карточках городов от следов клонирования.
 *
 * Почта города теперь главнее общей: что заведено в «Контактной информации»,
 * то и на сайте. Поэтому оставшиеся от сайта-донора адреса (almaty@, astana@,
 * info@vizitexpert.*) снова полезли бы на страницу контактов.
 *
 * Правило: если у города есть хоть один адрес, отличный от общего из настроек,
 * поля очищаются целиком — город начинает наследовать общую почту. Свой адрес
 * админ заводит заново в «Контактной информации», и он будет выведен.
 */
return new class extends Migration
{
    public function up(): void
    {
        $common = trim((string) DB::table('moonshine_settings')->value('email'));

        if ($common === '') {
            return;
        }

        foreach (DB::table('contacts')->get() as $row) {
            $rows = json_decode((string) $row->data_email, true) ?: [];

            $emails = array_map(
                static fn ($item) => trim((string) ($item['jt1'] ?? '')),
                is_array($rows) ? $rows : []
            );

            $emails[] = trim((string) ($row->email ?? ''));
            $emails = array_filter($emails);

            // ничего своего не заведено или всё совпадает с общим — не трогаем
            if (array_diff($emails, [$common]) === []) {
                continue;
            }

            DB::table('contacts')
                ->where('id', $row->id)
                ->update(['email' => null, 'data_email' => null]);
        }
    }

    public function down(): void
    {
        // Прежние адреса были следами клона, восстанавливать нечего.
    }
};
