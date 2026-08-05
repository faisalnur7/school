<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('chat_conversation_participants')) {
            Schema::create('chat_conversation_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chat_conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->boolean('is_admin')->default(false);
                $table->timestamp('muted_at')->nullable();
                $table->timestamp('last_read_at')->nullable();
                $table->timestamps();
            });
        }

        if (! $this->hasIndex('chat_conversation_participants', 'chat_conv_participant_unique')) {
            Schema::table('chat_conversation_participants', function (Blueprint $table) {
                $table->unique(['chat_conversation_id', 'user_id'], 'chat_conv_participant_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_conversation_participants');
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return false;
        }

        $database = DB::getDatabaseName();

        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();

        return $exists;
    }
};
