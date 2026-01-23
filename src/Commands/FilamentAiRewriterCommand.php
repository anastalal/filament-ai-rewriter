<?php

namespace Anastalal\FilamentAiRewriter\Commands;

use Illuminate\Console\Command;

class FilamentAiRewriterCommand extends Command
{
    public $signature = 'filament-ai-rewriter';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
