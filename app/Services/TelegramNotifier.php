<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramNotifier
{
    public function __construct(
        private ?string $botToken = null,
        private ?string $chatId = null,
    ) {
        $this->botToken ??= config('services.telegram.bot_token');
        $this->chatId ??= config('services.telegram.chat_id');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->botToken) && ! empty($this->chatId);
    }

    public function notifyDownloadRequest(array $data): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        $emoji = match ($data['status'] ?? '') {
            'success' => '✅',
            'no_media' => '⚠️',
            'error' => '❌',
            default => '📥',
        };

        $message = $this->formatDownloadMessage($emoji, $data);

        $this->send($message);
    }

    private function formatDownloadMessage(string $emoji, array $data): string
    {
        $lines = [
            "{$emoji} Nueva solicitud de descarga",
            '',
            'URL: ' . mb_substr($data['url'] ?? '', 0, 300),
            'Plataforma: ' . ($data['platform'] ?? '—'),
            'Estado: ' . ($data['status'] ?? '—'),
        ];

        if (isset($data['items_count'])) {
            $lines[] = 'Items: ' . $data['items_count'];
        }

        if (! empty($data['error_message'])) {
            $lines[] = 'Error: ' . mb_substr($data['error_message'], 0, 400);
        }

        $lines[] = 'Host: ' . ($data['site_host'] ?? request()->getHost() ?? '—');
        $lines[] = 'IP: ' . ($data['ip_address'] ?? request()->ip() ?? '—');

        return implode("\n", $lines);
    }

    private function send(string $message): void
    {
        try {
            Http::timeout(5)->post(
                "https://api.telegram.org/bot{$this->botToken}/sendMessage",
                [
                    'chat_id' => $this->chatId,
                    'text' => $message,
                ]
            );
        } catch (\Throwable) {
            // Silently ignore to not break the download flow
        }
    }
}
