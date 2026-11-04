<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_campaign_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_subscriber_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('campaign');
            $table->timestamp('sent_at');

            $table->timestamps();

            $table->unique(
                ['newsletter_subscriber_id', 'campaign'],
                'newsletter_campaign_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_campaign_sends');
    }
};
