<?php

namespace Anastalal\FilamentAiRewriter\Facades;

use Anastalal\FilamentAiRewriter\Services\AiService;
use Illuminate\Support\Facades\Facade;

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
