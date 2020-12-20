<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified News Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases\Controller\FrontendModule;

use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Input;
use Contao\ModuleModel;
use Contao\ModuleNewsReader;
use Contao\News;
use Contao\NewsModel;
use InspiredMinds\ContaoUnifiedNewsAliases\UnifiedNewsAliases;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(NewsReaderModuleController::TYPE, category="news")
 */
class NewsReaderModuleController extends ModuleNewsReader
{
    public const TYPE = 'newsreader_unified_aliases';

    private $unifiedAliases;

    public function __construct(UnifiedNewsAliases $unifiedAliases)
    {
        $this->unifiedAliases = $unifiedAliases;
    }

    public function __invoke(ModuleModel $model, string $section): Response
    {
        parent::__construct($model, $section);

        return new Response($this->generate());
    }

    protected function compile(): void
    {
        $this->overrideItems();

        parent::compile();
    }

    private function overrideItems(): void
    {
        $news = NewsModel::findOneByAlias(Input::get('items', false, true));

        // Check if this is a valid news alias
        if (null === $news) {
            return;
        }

        // Check if unified aliases feature is enabled for this news
        if (!$this->unifiedAliases->isUnifiedAliasEnabled($news)) {
            return;
        }

        // Get the actual news for the current language
        $actualNews = $this->unifiedAliases->getNewsForCurrentLanguage($news);

        if (null === $actualNews) {
            return;
        }

        // Check if this news is actually allowed here
        if (null === NewsModel::findPublishedByParentAndIdOrAlias($actualNews->alias, $this->news_archives)) {
            return;
        }

        // Redirect in case the detail URL was accessed with the regular alias of the news
        if ($this->unifiedAliases->isCurrentLanguage($news) && !$this->unifiedAliases->isMainNews($news)) {
            $this->redirectToMainNewsUrl($news);
        }

        // Override the "items" variable
        Input::setGet('items', $actualNews->alias);
    }

    private function redirectToMainNewsUrl(NewsModel $news): void
    {
        $mainNews = $this->unifiedAliases->getMainNews($news);

        if (null === $mainNews) {
            return;
        }

        $news->preventSaving();

        $news->id = 'clone-'.$news->id;
        $news->alias = $mainNews->alias;

        throw new RedirectResponseException(News::generateNewsUrl($news, false, true), Response::HTTP_MOVED_PERMANENTLY);
    }
}
