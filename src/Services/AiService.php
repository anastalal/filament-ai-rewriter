<?php

namespace Anastalal\FilamentAiRewriter\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('filament-ai-rewriter');
    }

    public function rewrite(string $text, array $options = []): ?string
    {
        if (empty(trim($text))) {
            throw new \Exception(__('filament-ai-rewriter::filament-ai-rewriter.messages.empty'));
        }

        if (mb_strlen($text) > $this->config['ui']['char_limit']) {
            throw new \Exception(__('filament-ai-rewriter::filament-ai-rewriter.messages.too_long', [
                'limit' => $this->config['ui']['char_limit'],
            ]));
        }

        $provider = $options['provider'] ?? $this->config['default_provider'];
        $model = $options['model'] ?? $this->config['default_model'];
        $style = $options['style'] ?? $this->config['default_style'];
        $type = $options['type'] ?? 'text'; // 'text' or 'textarea'

        $localKeywords = $options['keywords'] ?? '';
        $globalKeywords = $this->config['global_keywords'] ?? '';

        $mergedKeywords = collect(explode(',', $localKeywords . ',' . $globalKeywords))
            ->map(fn ($k) => trim($k))
            ->filter()
            ->unique()
            ->implode(', ');

        $keywords = ! empty($mergedKeywords) ? $mergedKeywords : null;

        $cacheKey = $this->generateCacheKey($text, $provider, $model, $style, $type, $keywords);

        $cache = Cache::getFacadeRoot();
        if (method_exists($cache, 'tags')) {
            $cache = Cache::tags(['filament-ai-rewriter']);
        }

        if ($this->config['cache']['enabled'] && $cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        try {
            $result = $this->callApi($text, $provider, $model, $style, $options, $type, $keywords);

            if ($result) {
                $result = trim($result, " \"\n\r\t");

                if ($this->config['cache']['enabled']) {
                    $cache->put($cacheKey, $result, $this->config['cache']['ttl']);
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('AI Rewrite Error', [
                'provider' => $provider,
                'model' => $model,
                'style' => $style,
                'type' => $type,
                'text_length' => mb_strlen($text),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception(__('filament-ai-rewriter::filament-ai-rewriter.messages.api_error', [
                'message' => $e->getMessage(),
            ]));
        }
    }

    protected function callApi(string $text, string $provider, string $model, string $style, array $options, string $type, ?string $keywords = null): ?string
    {
        $method = 'call' . ucfirst($provider) . 'Api';

        if (! method_exists($this, $method)) {
            throw new \Exception("Unsupported AI provider: {$provider}");
        }

        return $this->$method($text, $model, $style, $options, $type, $keywords);
    }

    protected function callOpenaiApi(string $text, string $model, string $style, array $options, string $type, ?string $keywords = null): ?string
    {
        $apiKey = $this->config['providers']['openai']['api_key'] ?? null;

        if (! $apiKey) {
            throw new \Exception(__('filament-ai-rewriter::filament-ai-rewriter.messages.config_error'));
        }

        $maxTokens = $options['max_tokens']
            ?? $this->config['prompts']['field_limits'][$type]
            ?? $this->config['prompts']['field_limits']['text']
            ?? $this->config['max_tokens'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout($this->config['timeout'])
            ->post($this->config['providers']['openai']['base_url'] . '/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt($style, $type, $keywords),
                    ],
                    [
                        'role' => 'user',
                        'content' => $text,
                    ],
                ],
                'temperature' => $options['temperature'] ?? $this->config['temperature'],
                'max_tokens' => (int) $maxTokens,
            ]);

        if ($response->failed()) {
            throw new \Exception($response->json()['error']['message'] ?? $response->body());
        }

        return $response->json()['choices'][0]['message']['content'] ?? null;
    }

    protected function callGeminiApi(string $text, string $model, string $style, array $options, string $type, ?string $keywords = null): ?string
    {
        $apiKey = $this->config['providers']['gemini']['api_key'] ?? null;

        if (! $apiKey) {
            throw new \Exception(__('filament-ai-rewriter::filament-ai-rewriter.messages.config_error'));
        }

        $maxTokens = $options['max_tokens']
            ?? $this->config['prompts']['field_limits'][$type]
            ?? $this->config['prompts']['field_limits']['text']
            ?? $this->config['max_tokens'];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout($this->config['timeout'])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $this->getSystemPrompt($style, $type, $keywords) . "\n\nText to rewrite:\n" . $text],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => $options['temperature'] ?? $this->config['temperature'],
                    'maxOutputTokens' => (int) $maxTokens,
                ],
            ]);

        if ($response->failed()) {
            throw new \Exception($response->json()['error']['message'] ?? $response->body());
        }

        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    protected function callAnthropicApi(string $text, string $model, string $style, array $options, string $type, ?string $keywords = null): ?string
    {
        $apiKey = $this->config['providers']['anthropic']['api_key'] ?? null;

        if (! $apiKey) {
            throw new \Exception(__('filament-ai-rewriter::filament-ai-rewriter.messages.config_error'));
        }

        $maxTokens = $options['max_tokens']
            ?? $this->config['prompts']['field_limits'][$type]
            ?? $this->config['prompts']['field_limits']['text']
            ?? $this->config['max_tokens'];

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout($this->config['timeout'])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $model,
                'max_tokens' => (int) $maxTokens,
                'system' => $this->getSystemPrompt($style, $type, $keywords),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $text,
                    ],
                ],
                'temperature' => $options['temperature'] ?? $this->config['temperature'],
            ]);

        if ($response->failed()) {
            throw new \Exception($response->json()['error']['message'] ?? $response->body());
        }

        return $response->json()['content'][0]['text'] ?? null;
    }

    protected function getSystemPrompt(string $style, string $type = 'text', ?string $keywords = null): string
    {
        $system = $this->config['prompts']['system'];
        $stylePrompt = $this->config['prompts']['styles'][$style] ?? $this->config['prompts']['styles']['improve'];

        $typeInstruction = $this->config['prompts']['length_instructions'][$type]
            ?? $this->config['prompts']['length_instructions']['text'];

        $prompt = "{$system}\n\nStyle: {$stylePrompt}\nContext: {$typeInstruction}";

        if (! empty($keywords)) {
            $keywordsInstruction = str_replace(':keywords', $keywords, $this->config['prompts']['keywords_instruction']);
            $prompt .= "\n\n{$keywordsInstruction}";
        }

        return $prompt;
    }

    protected function getUserPrompt(string $text, string $style): string
    {
        // Not used anymore as we moved logic to system prompt
        return $text;
    }

    protected function generateCacheKey(string $text, string $provider, string $model, string $style, string $type, ?string $keywords = null): string
    {
        return 'ai_rewrite:' . md5($text . $provider . $model . $style . $type . ($keywords ?? ''));
    }

    public function getAvailableProviders(): array
    {
        return array_keys($this->config['providers']);
    }

    public function getAvailableModels(string $provider): array
    {
        return $this->config['providers'][$provider]['models'] ?? [];
    }

    public function getAvailableStyles(): array
    {
        return array_keys($this->config['styles']);
    }
}
