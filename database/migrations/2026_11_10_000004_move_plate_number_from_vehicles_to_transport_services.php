<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vehicles') || !Schema::hasTable('transport_services')) {
            return;
        }

        if (!Schema::hasColumn('transport_services', 'plate_number')) {
            Schema::table('transport_services', function (Blueprint $table) {
                $table->string('plate_number', 30)->nullable()->after('vehicle_id');
            });
        }

        if (Schema::hasColumn('vehicles', 'plate_number')) {
            $vehiclePlates = DB::table('vehicles')->pluck('plate_number', 'id')->all();
            DB::table('transport_services')
                ->whereNull('plate_number')
                ->orderBy('id')
                ->chunkById(500, function ($services) use ($vehiclePlates) {
                    foreach ($services as $service) {
                        $plateNumber = $vehiclePlates[$service->vehicle_id] ?? null;

                        if ($plateNumber !== null) {
                            DB::table('transport_services')
                                ->where('id', $service->id)
                                ->update(['plate_number' => $plateNumber]);
                        }
                    }
                });

            $plateNumberIndex = collect(Schema::getIndexes('vehicles'))
                ->first(fn (array $index) => $index['unique'] && $index['columns'] === ['plate_number']);

            Schema::table('vehicles', function (Blueprint $table) use ($plateNumberIndex) {
                if ($plateNumberIndex) {
                    $table->dropUnique($plateNumberIndex['name']);
                }

                $table->dropColumn('plate_number');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('vehicles') || !Schema::hasTable('transport_services')) {
            return;
        }

        if (!Schema::hasColumn('vehicles', 'plate_number')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->string('plate_number', 30)->nullable()->after('name');
            });
        }

        if (Schema::hasColumn('transport_services', 'plate_number')) {
            DB::table('transport_services')
                ->whereNotNull('plate_number')
                ->orderBy('id')
                ->get(['vehicle_id', 'plate_number'])
                ->each(function ($service) {
                    DB::table('vehicles')
                        ->where('id', $service->vehicle_id)
                        ->whereNull('plate_number')
                        ->update(['plate_number' => $service->plate_number]);
                });

            Schema::table('transport_services', function (Blueprint $table) {
                $table->dropColumn('plate_number');
            });
        }
    }
};
