<?php

return [
    'button' => [
        'rewrite' => 'Rewrite with AI',
        'rewriting' => 'Rewriting...',
        'select_style' => 'Select writing style',
    ],

    'styles' => [
        'improve' => 'Improve writing',
        'professional' => 'Professional tone',
        'casual' => 'Casual tone',
        'simplify' => 'Simplify text',
        'expand' => 'Expand text',
        'summarize' => 'Summarize text',
        'translate_en' => 'Translate to English',
        'translate_ar' => 'Translate to Arabic',
        'creative' => 'Creative writing',
        'formal' => 'Formal writing',
    ],

    'messages' => [
        'success' => 'Text rewritten successfully!',
        'error' => 'Failed to rewrite text. Please try again.',
        'empty' => 'Please enter some text first.',
        'too_long' => 'Text is too long. Maximum :limit characters.',
        'api_error' => 'API error: :message',
        'config_error' => 'Please configure AI API keys in your .env file.',
    ],

    'settings' => [
        'provider' => 'AI Provider',
        'model' => 'AI Model',
        'style' => 'Writing Style',
        'temperature' => 'Creativity (0-1)',
        'max_tokens' => 'Max Tokens',
    ],

    'commands' => [
        'clear_cache' => [
            'description' => 'Clear all AI rewrite cached results',
            'success' => 'AI Rewrite cache cleared successfully.',
        ],
    ],
];
