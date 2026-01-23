# Filament AI Rewriter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/anastalal/filament-ai-rewriter.svg?style=flat-square)](https://packagist.org/packages/anastalal/filament-ai-rewriter)
[![Total Downloads](https://img.shields.io/packagist/dt/anastalal/filament-ai-rewriter.svg?style=flat-square)](https://packagist.org/packages/anastalal/filament-ai-rewriter)
[![License](https://img.shields.io/packagist/l/anastalal/filament-ai-rewriter.svg?style=flat-square)](https://packagist.org/packages/anastalal/filament-ai-rewriter)

AI-powered content rewriting for FilamentPHP. Enhance your forms with intelligent text refinement, creative expansion, and natural translations directly within your Filament fields.

![Filament AI Rewriter](https://raw.githubusercontent.com/anastalal/filament-ai-rewriter/main/art/banner.png)

## Features

- **Multi-Provider Support**: Seamlessly switch between OpenAI (GPT-4/3.5), Google Gemini, and Anthropic Claude.
- **Unified Macro API**: Add AI functionality to any text-based field using a simple `->withAi()` macro.
- **Smart Input Filtering**: Automatically hides AI actions on sensitive or strictly formatted fields (Password, Email, Numeric, etc.) to ensure data integrity.
- **Context-Aware Lengths**: Intelligent response limits tailored to the field type (concise for `TextInput`, detailed for `Textarea` and Editors).
- **SEO Keywords**: Naturally incorporate global or field-specific keywords into the rewritten content.
- **No Manual Bolding**: Explicitly instructs AI to avoid unnecessary bolding or highlighting of keywords.
- **Selective Cache Management**: Comes with a CLI utility to clear only AI-generated results from your cache.
- **Fully Multilingual**: Native support for English and Arabic.

## Support

- **Filament**: v3.x, v4.x, and v5.x
- **PHP**: ^8.1

## Installation

You can install the package via composer:

```bash
composer require anastalal/filament-ai-rewriter
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="filament-ai-rewriter-config"
```

## Configuration

Add your API keys to your `.env` file:

```env
AI_REWRITER_PROVIDER=openai
OPENAI_API_KEY=your-api-key-here

# Optional: Global SEO keywords
AI_REWRITER_GLOBAL_KEYWORDS="your, main, keywords"
```

The configuration file allows you to customize providers, models, default styles, and detailed AI prompts.

## Usage

### Simple Usage

Add AI rewrite capability to any `TextInput`, `Textarea`, `RichEditor`, or `MarkdownEditor`:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->withAi()
```

### Advanced Usage

Customize the AI behavior for specific fields:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->withAi([
        'style' => 'creative',
        'keywords' => 'sale, unique, offer',
        'temperature' => 0.8,
        'max_tokens' => 2000,
    ])
```

### Global Keywords

Keywords set in `config/filament-ai-rewriter.php` are automatically used in every request. Keywords provided via the `withAi()` macro will be merged with the global ones.

## CLI Commands

To clear all AI-generated results from the cache:

```bash
php artisan filament-ai-rewriter:clear-cache
```

*Note: Selective clearing requires a cache driver that supports tags (e.g., Redis or Memcached). You can use `--force` to clear the entire cache if needed.*

## Testing

```bash
composer test
```

## Credits

- [anastalal](https://github.com/anastalal)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
