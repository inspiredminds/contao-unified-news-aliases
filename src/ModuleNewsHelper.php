<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases;

use Contao\ModuleNews;
use Contao\NewsModel;

/**
 * Helper class to expose ModuleNews::generateLink().
 */
class ModuleNewsHelper extends ModuleNews
{
    public function __construct()
    {
        // Noop
    }

    public function generateHtmlLink(string $linkText, NewsModel $news, bool $addArchive = false, $isReadMore = false): string
    {
        return $this->generateLink($linkText, $news, $addArchive, $isReadMore);
    }

    protected function compile(): void
    {
        // Noop
    }
}
