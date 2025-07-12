<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize users.phone
        DB::table('users')->get()->each(function ($user) {
            $normalized = self::normalizePhone($user->phone);
            DB::table('users')
                ->where('id', $user->id)
                ->update(['phone' => $normalized]);
        });

        // Normalize guests.phone
        DB::table('guests')->get()->each(function ($guest) {
            $normalized = self::normalizePhone($guest->phone);
            DB::table('guests')
                ->where('id', $guest->id)
                ->update(['phone' => $normalized]);
        });
    }

    public function down(): void
    {
        // Невозможно восстановить старый формат, поэтому этот метод остаётся пустым
    }

    private static function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }
        // Оставляем только цифры
        $digits = preg_replace('/\D/', '', $phone);

        // Если номер в украинском формате 0671234567
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = '38'.$digits; // превращаем в 380671234567
        }

        // Добавляем +
        return '+'.$digits;
    }
};
