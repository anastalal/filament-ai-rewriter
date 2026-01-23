<?php

namespace Anastalal\FilamentAiRewriter\Facades;

use Illuminate\Support\Facades\Facade;
use Anastalal\FilamentAiRewriter\Services\AiService;

/**
 * @see \Anastalal\FilamentAiRewriter\FilamentAiRewriter
 */
class FilamentAiRewriter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AiService::class;
    }
}
