<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified News Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases;

use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\RequestStack;

class UnifiedNewsAliases
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Check whether the main news archive for the given news has unified aliases enabled.
     */
    public function isUnifiedAliasEnabled(NewsModel $news): bool
    {
        $archive = NewsArchiveModel::findById((int) $news->pid);

        if ((int) $archive->master > 0) {
            $archive = NewsArchiveModel::findById((int) $archive->master);
        }

        if (null === $archive) {
            return false;
        }

        return (bool) $archive->use_unified_aliases;
    }

    /**
     * Checks whethe the given news article is for the current language.
     */
    public function isCurrentLanguage(NewsModel $news): bool
    {
        $archive = NewsArchiveModel::findById((int) $news->pid);

        if (null === $archive) {
            throw new \RuntimeException('Could not find archive for news ID "'.$news->id.'".');
        }

        $target = PageModel::findWithDetails($archive->jumpTo);

        if (null === $target) {
            throw new \RuntimeException('Could not find target page for news archive ID "'.$archive->id.'".');
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \RuntimeException('Could not get current request.');
        }

        return $target->rootLanguage === $request->getLocale();
    }

    /**
     * Checks whether the given news is the main news article.
     */
    public function isMainNews(NewsModel $news): bool
    {
        $archive = NewsArchiveModel::findById($news->pid);

        if (null === $archive) {
            throw new \RuntimeException('Could not find archive for news ID "'.$news->id.'".');
        }

        return 0 === (int) $archive->master;
    }

    /**
     * Returns the main news record for the given news, if applicable.
     */
    public function getMainNews(NewsModel $news): ?NewsModel
    {
        if (0 === (int) $news->languageMain) {
            return null;
        }

        return NewsModel::findById((int) $news->languageMain);
    }

    /**
     * Returns the associated news for the given news and language.
     */
    public function getNewsForLanguage(NewsModel $news, string $language): ?NewsModel
    {
        $searchId = (int) ($news->languageMain ?: $news->id);
        $t = NewsModel::getTable();
        $articles = NewsModel::findBy(
            ["($t.id = ? OR $t.languageMain = ?)"],
            [$searchId, $searchId]
        );

        if (null === $articles) {
            return null;
        }

        foreach ($articles as $article) {
            $archive = NewsArchiveModel::findById((int) $article->pid);

            if (null === $archive) {
                continue;
            }

            $target = PageModel::findWithDetails($archive->jumpTo);

            if (null === $target) {
                return null;
            }

            if ($target->rootLanguage === $language) {
                return $article;
            }
        }

        return null;
    }

    /**
     * Returns the associated news for the given news and the current language.
     */
    public function getNewsForCurrentLanguage(NewsModel $news): ?NewsModel
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \RuntimeException('Could not get current request.');
        }

        return $this->getNewsForLanguage($news, $request->getLocale());
    }
}
