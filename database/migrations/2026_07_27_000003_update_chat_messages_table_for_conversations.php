<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreignId('chat_conversation_id')->nullable()->after('id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->after('chat_conversation_id')->constrained('users')->cascadeOnDelete();
            $table->text('body')->nullable()->after('sender_id');
            $table->string('attachment_path')->nullable()->after('body');
            $table->string('attachment_name')->nullable()->after('attachment_path');
            $table->string('attachment_mime', 150)->nullable()->after('attachment_name');
            $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_mime');
            $table->boolean('is_system')->default(false)->after('attachment_size');

            $table->index(['chat_conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex(['chat_conversation_id', 'created_at']);
            $table->dropConstrainedForeignId('chat_conversation_id');
            $table->dropConstrainedForeignId('sender_id');
            $table->dropColumn([
                'body',
                'attachment_path',
                'attachment_name',
                'attachment_mime',
                'attachment_size',
                'is_system',
            ]);
        });
    }
};
