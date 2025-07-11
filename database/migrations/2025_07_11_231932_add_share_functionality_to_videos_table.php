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
        Schema::table('videos', function (Blueprint $table) {
            $table->string('share_token')->nullable()->unique()->after('vid');
            $table->boolean('is_shareable')->default(false)->after('share_token');
            $table->timestamp('share_expires_at')->nullable()->after('is_shareable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['share_token', 'is_shareable', 'share_expires_at']);
        });
    }
};
