<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchUuidColumnToActivityLogTable extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn(config('activitylog.table_name'), 'batch_uuid')) {
            return;
        }

        Schema::connection(config('activitylog.database_connection'))
            ->table(config('activitylog.table_name'), function (Blueprint $table) {
                $table->uuid('batch_uuid')->nullable()->after('properties');
            });
    }
}