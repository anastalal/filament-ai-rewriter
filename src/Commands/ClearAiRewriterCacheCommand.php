<?php

namespace Anastalal\FilamentAiRewriter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearAiRewriterCacheCommand extends Command
{
    public $signature = 'filament-ai-rewriter:clear-cache {--force : Force clearing the entire cache if tags are not supported}';

    public $description = 'Clear all AI rewrite cached results';

    public function handle(): int
    {
        $cache = Cache::getFacadeRoot();
        
        if (method_exists($cache, 'tags')) {
            $this->info('Finding and clearing AI rewrite cache entries via tags...');
            Cache::tags(['filament-ai-rewriter'])->flush();
            $this->info(__('filament-ai-rewriter::filament-ai-rewriter.commands.clear_cache.success'));
            return self::SUCCESS;
        }

        $this->warn('Selective cache clearing is only supported by drivers that support tags (e.g., redis, memcached).');
        
        if ($this->option('force')) {
            if ($this->confirm('Your cache driver does not support tags. This will clear the ENTIRE application cache. Proceed?', false)) {
                Cache::flush();
                $this->info('Entire application cache cleared.');
                return self::SUCCESS;
            }
            
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        $this->error('Failed to clear cache: Driver does not support tags. Use --force to clear the entire cache instead.');
        
        return self::FAILURE;
    }
}
