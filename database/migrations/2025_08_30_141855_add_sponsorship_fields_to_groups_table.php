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
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('sponsorships_enabled')->default(false);
            $table->json('sponsorship_packages')->nullable();
            $table->string('sponsorship_contact_email')->nullable();
            $table->text('sponsorship_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn([
                'sponsorships_enabled',
                'sponsorship_packages', 
                'sponsorship_contact_email',
                'sponsorship_description'
            ]);
        });
    }
};
