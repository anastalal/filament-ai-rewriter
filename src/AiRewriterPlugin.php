<?php

namespace Anastalal\FilamentAiRewriter;

use Filament\Contracts\Plugin;
use Filament\Panel;

use function Filament\filament;

class AiRewriterPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-ai-rewriter';
    }

    public function register(Panel $panel): void
    {
        // Registration logic if needed
    }

    public function boot(Panel $panel): void
    {
        // Boot logic if needed
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
