<?php

namespace Anastalal\FilamentAiRewriter;

use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAiRewriterServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-ai-rewriter';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function ($command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('anastalal/filament-ai-rewriter');
            });
    }

    public function packageBooted(): void
    {
        // Register Form Components
        $this->registerFormComponents();
    }

    protected function registerFormComponents(): void
    {
        $macro = function (array $options = []) {
            /** @var TextInput|Textarea $this */
            $this->hintAction(
                function ($component) use ($options) {
                    if ($component instanceof TextInput) {
                        $type = $component->getType();
                        $restrictedTypes = ['email', 'numeric', 'integer', 'password', 'tel', 'url'];

                        if (in_array($type, $restrictedTypes)) {
                            return null;
                        }
                    }

                    return Action::make('ai_rewrite')
                        ->label(__('filament-ai-rewriter::filament-ai-rewriter.button.rewrite'))
                        ->tooltip(__('filament-ai-rewriter::filament-ai-rewriter.button.rewrite'))
                        ->icon('heroicon-m-sparkles')
                        ->color('primary')
                        ->action(function () use ($component, $options) {
                            $state = $component->getState();
                            $type = 'text';

                            if ($component instanceof Textarea) {
                                $type = 'textarea';
                            } elseif (class_exists(RichEditor::class) && $component instanceof RichEditor) {
                                $type = 'richeditor';
                            } elseif (class_exists(MarkdownEditor::class) && $component instanceof MarkdownEditor) {
                                $type = 'markdown';
                            }

                            if (empty(trim($state ?? ''))) {
                                Notification::make()
                                    ->title(__('filament-ai-rewriter::filament-ai-rewriter.messages.empty'))
                                    ->warning()
                                    ->send();

                                return;
                            }

                            try {
                                $service = app(\Anastalal\FilamentAiRewriter\Services\AiService::class);

                                $rewrittenText = $service->rewrite($state, [
                                    'provider' => $options['provider'] ?? null,
                                    'model' => $options['model'] ?? null,
                                    'style' => $options['style'] ?? null,
                                    'temperature' => $options['temperature'] ?? null,
                                    'max_tokens' => $options['max_tokens'] ?? null,
                                    'type' => $type,
                                    'keywords' => $options['keywords'] ?? null,
                                ]);

                                if ($rewrittenText) {
                                    $component->state($rewrittenText);

                                    Notification::make()
                                        ->title(__('filament-ai-rewriter::filament-ai-rewriter.messages.success'))
                                        ->success()
                                        ->send();
                                }
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title(__('filament-ai-rewriter::filament-ai-rewriter.messages.error'))
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        });
                }
            );

            return $this;
        };

        TextInput::macro('withAi', $macro);
        Textarea::macro('withAi', $macro);

        if (class_exists(RichEditor::class)) {
            RichEditor::macro('withAi', $macro);
        }

        if (class_exists(MarkdownEditor::class)) {
            MarkdownEditor::macro('withAi', $macro);
        }
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            Commands\ClearAiRewriterCacheCommand::class,
        ];
    }
}
