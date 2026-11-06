<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone', 40)->nullable()->after('email');
                $table->index('phone', 'idx_users_phone');
            });
        }

        if (Schema::hasTable('reservations')) {
            DB::table('users')->whereNull('phone')->orderBy('id')->get()->each(function ($user) {
                $phone = DB::table('reservations')
                    ->where('user_id', $user->id)
                    ->whereNotNull('customer_phone')
                    ->where('customer_phone', '<>', '')
                    ->latest('id')
                    ->value('customer_phone');

                if ($phone) {
                    DB::table('users')->where('id', $user->id)->update(['phone' => $phone]);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('idx_users_phone');
                $table->dropColumn('phone');
            });
        }
    }
};
