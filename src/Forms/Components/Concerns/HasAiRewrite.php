<?php

namespace Anastalal\FilamentAiRewriter\Forms\Components\Concerns;

use Closure;

trait HasAiRewrite
{
    protected string | Closure | null $aiProvider = null;

    protected string | Closure | null $aiModel = null;

    protected string | Closure | null $aiStyle = null;

    protected float | Closure | null $aiTemperature = null;

    protected int | Closure | null $aiMaxTokens = null;

    protected string | Closure | null $aiKeywords = null;

    protected array $aiStyles = [];

    protected bool $showStyleSelector = true;

    protected function initializeAiRewrite(): void {}

    public function bootAiRewrite(): void {}

    public function rewriteWithAI(?string $style = null): void
    {
        $style = $style ?? $this->getAiStyle();

        $this->dispatch(
            'ai-rewriting',
            statePath: $this->getStatePath()
        );

        try {
            $service = app(\Anastalal\FilamentAiRewriter\Services\AiService::class);
            $type = 'text';

            if ($this instanceof \Filament\Forms\Components\Textarea) {
                $type = 'textarea';
            } elseif (class_exists(\Filament\Forms\Components\RichEditor::class) && $this instanceof \Filament\Forms\Components\RichEditor) {
                $type = 'richeditor';
            } elseif (class_exists(\Filament\Forms\Components\MarkdownEditor::class) && $this instanceof \Filament\Forms\Components\MarkdownEditor) {
                $type = 'markdown';
            }

            $rewrittenText = $service->rewrite($this->getState(), [
                'provider' => $this->getAiProvider(),
                'model' => $this->getAiModel(),
                'style' => $style,
                'temperature' => $this->getAiTemperature(),
                'max_tokens' => $this->getAiMaxTokens(),
                'type' => $type,
                'keywords' => $this->getAiKeywords(),
            ]);

            if ($rewrittenText) {
                $this->state($rewrittenText);

                \Filament\Notifications\Notification::make()
                    ->title(__('filament-ai-rewriter::filament-ai-rewriter.messages.success'))
                    ->success()
                    ->send();

                $this->dispatch(
                    'ai-rewrite-success',
                    statePath: $this->getStatePath()
                );
            }
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title(__('filament-ai-rewriter::filament-ai-rewriter.messages.error'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->dispatch(
                'ai-rewrite-error',
                statePath: $this->getStatePath()
            );
        }
    }

    public function aiProvider(string | Closure | null $provider): static
    {
        $this->aiProvider = $provider;

        return $this;
    }

    public function aiModel(string | Closure | null $model): static
    {
        $this->aiModel = $model;

        return $this;
    }

    public function aiStyle(string | Closure | null $style): static
    {
        $this->aiStyle = $style;

        return $this;
    }

    public function aiTemperature(float | Closure | null $temperature): static
    {
        $this->aiTemperature = $temperature;

        return $this;
    }

    public function aiMaxTokens(int | Closure | null $maxTokens): static
    {
        $this->aiMaxTokens = $maxTokens;

        return $this;
    }

    public function aiKeywords(string | Closure | null $keywords): static
    {
        $this->aiKeywords = $keywords;

        return $this;
    }

    public function aiStyles(array $styles): static
    {
        $this->aiStyles = $styles;

        return $this;
    }

    public function hideStyleSelector(bool $condition = true): static
    {
        $this->showStyleSelector = ! $condition;

        return $this;
    }

    public function getAiProvider(): ?string
    {
        return $this->evaluate($this->aiProvider) ?? config('filament-ai-rewriter.default_provider');
    }

    public function getAiModel(): ?string
    {
        return $this->evaluate($this->aiModel) ?? config('filament-ai-rewriter.default_model');
    }

    public function getAiStyle(): ?string
    {
        return $this->evaluate($this->aiStyle) ?? config('filament-ai-rewriter.default_style');
    }

    public function getAiTemperature(): ?float
    {
        return $this->evaluate($this->aiTemperature) ?? config('filament-ai-rewriter.temperature');
    }

    public function getAiMaxTokens(): ?int
    {
        return $this->evaluate($this->aiMaxTokens) ?? config('filament-ai-rewriter.max_tokens');
    }

    public function getAiKeywords(): ?string
    {
        return $this->evaluate($this->aiKeywords);
    }

    public function getAvailableAiStyles(): array
    {
        if (! empty($this->aiStyles)) {
            $translated = [];
            foreach ($this->aiStyles as $key) {
                $translated[$key] = __('filament-ai-rewriter::filament-ai-rewriter.styles.' . $key);
            }

            return $translated;
        }

        $styles = config('filament-ai-rewriter.styles', []);
        $translatedStyles = [];

        foreach ($styles as $key => $label) {
            $translatedStyles[$key] = __('filament-ai-rewriter::filament-ai-rewriter.styles.' . $key);
        }

        return $translatedStyles;
    }

    public function shouldShowStyleSelector(): bool
    {
        return $this->showStyleSelector && count($this->getAvailableAiStyles()) > 1;
    }
}
