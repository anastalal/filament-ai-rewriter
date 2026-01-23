<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | Supported: "openai", "gemini", "anthropic"
    |
    */
    'default_provider' => env('AI_REWRITER_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Default AI Model
    |--------------------------------------------------------------------------
    |
    | Default model to use for text rewriting.
    |
    */
    'default_model' => env('AI_REWRITER_MODEL', 'gpt-3.5-turbo'),

    /*
    |--------------------------------------------------------------------------
    | Default Writing Style
    |--------------------------------------------------------------------------
    */
    'default_style' => 'improve',

    /*
    |--------------------------------------------------------------------------
    | SEO Global Keywords
    |--------------------------------------------------------------------------
    |
    | These keywords will be included in all AI rewrite requests unless
    | overridden at the component level.
    |
    */
    'global_keywords' => env('AI_REWRITER_GLOBAL_KEYWORDS', ''),

    /*
    |--------------------------------------------------------------------------
    | AI Providers Configuration
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'organization' => env('OPENAI_ORGANIZATION'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'models' => [
                'gpt-4' => 'GPT-4',
                'gpt-4-turbo' => 'GPT-4 Turbo',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                'gpt-3.5-turbo-instruct' => 'GPT-3.5 Turbo Instruct',
            ],
        ],

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'models' => [
                'gemini-pro' => 'Gemini Pro',
                'gemini-pro-vision' => 'Gemini Pro Vision',
            ],
        ],

        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'models' => [
                'claude-3-opus' => 'Claude 3 Opus',
                'claude-3-sonnet' => 'Claude 3 Sonnet',
                'claude-3-haiku' => 'Claude 3 Haiku',
                'claude-2.1' => 'Claude 2.1',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Writing Styles
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Default Parameters
    |--------------------------------------------------------------------------
    */
    'temperature' => 0.7,
    'max_tokens' => 1000,
    'timeout' => 30,

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'button_label' => 'Rewrite with AI',
        'loading_label' => 'Rewriting...',
        'success_message' => 'Text rewritten successfully!',
        'error_message' => 'Failed to rewrite text. Please try again.',
        'char_limit' => 5000,
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Prompts
    |--------------------------------------------------------------------------
    |
    | Custom prompts for the AI rewriter.
    |
    */
    'prompts' => [
        'system' => "You are a professional text editor. Your task is to rewrite the text provided by the user according to the requested style.\n\nCRITICAL: Return ONLY the rewritten text. \nDO NOT include any explanations, greetings, or introductory phrases like \"Here is the result:\". \nYour response will directly replace the user's input. \nEnsure the text is complete and does not end abruptly in the middle of a sentence.",

        'styles' => [
            'improve' => 'Improve the writing while keeping the original meaning and tone.',
            'professional' => 'Rewrite the text in a professional, business-appropriate tone.',
            'casual' => 'Rewrite the text in a friendly, casual tone.',
            'simplify' => 'Simplify the text to make it easier to understand for a general audience.',
            'expand' => 'Expand the text by adding more relevant details and information.',
            'summarize' => 'Summarize the text concisely while keeping the most important information.',
            'translate_en' => 'Translate the text to English accurately and naturally.',
            'translate_ar' => 'Translate the text to Arabic accurately and naturally.',
            'creative' => 'Rewrite the text in a creative and engaging way.',
            'formal' => 'Rewrite the text in a formal, academic tone.',
        ],

        'length_instructions' => [
            'text' => 'This is for a single-line text input. Keep the response extremely concise, strictly on a single line, and roughly the same length as the input text.',
            'textarea' => 'This is for a multi-line text area. You MUST provide a more detailed, richer, and longer response than the input. Use multiple sentences and paragraphs to thoroughly cover the content. Do not be brief.',
            'richeditor' => 'This is for a rich text editor. Provide a comprehensive, well-structured, and substantial response with rich detail and professional formatting. Use multiple paragraphs to expand on the ideas provided. We need a long and professional result.',
            'markdown' => 'This is for a markdown editor. Provide a detailed and expanded response. You may use markdown formatting (like lists or headers) to structure the content effectively and ensure it is substantial in length.',
        ],

        'field_limits' => [
            'text' => 200,
            'textarea' => 1500,
            'richeditor' => 2500,
            'markdown' => 2500,
        ],

        'keywords_instruction' => "IMPORTANT SEO REQUIREMENT: Naturally incorporate the following keywords: :keywords. \nCRITICAL: DO NOT bold, highlight, or add any special formatting (like **) to these keywords. They must appear as normal text within the rewritten content.",
    ],
];
