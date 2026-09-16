<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Общая почта компании в «Настройках сайта → Контакты».
 *
 * Раньше её негде было задать: в moonshine_settings лежали только телефоны
 * и адрес, а на странице контактов почта бралась из карточек городов/стран.
 * У сайтов-клонов там осталась чужая почта (almaty@, astana@).
 */
return new class extends Migration
{
    private const EMAIL = 'info@hottour.com';

    public function up(): void
    {
        if (! Schema::hasColumn('moonshine_settings', 'email')) {
            Schema::table('moonshine_settings', function (Blueprint $table) {
                $table->string('email')->nullable()->after('phone2');
            });
        }

        // Пустое поле — значит на странице контактов почты не будет вовсе,
        // поэтому сразу проставляем рабочий адрес.
        DB::table('moonshine_settings')
            ->where(fn ($q) => $q->whereNull('email')->orWhere('email', ''))
            ->update(['email' => self::EMAIL]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('moonshine_settings', 'email')) {
            Schema::table('moonshine_settings', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }
};
