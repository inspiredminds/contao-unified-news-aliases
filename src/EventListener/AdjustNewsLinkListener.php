<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified News Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\FrontendTemplate;
use Contao\News;
use Contao\NewsModel;
use InspiredMinds\ContaoUnifiedNewsAliases\ModuleNewsHelper;
use InspiredMinds\ContaoUnifiedNewsAliases\UnifiedNewsAliases;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Hook("parseArticles")
 */
class AdjustNewsLinkListener
{
    private $unifiedNewsAliases;
    private $newsHelper;
    private $translator;

    public function __construct(UnifiedNewsAliases $unifiedNewsAliases, TranslatorInterface $translator)
    {
        $this->unifiedNewsAliases = $unifiedNewsAliases;
        $this->newsHelper = new ModuleNewsHelper();
        $this->translator = $translator;
    }

    public function __invoke(FrontendTemplate $template, array $newsEntry): void
    {
        $news = NewsModel::findById($newsEntry['id']);

        if (!$this->unifiedNewsAliases->isUnifiedAliasEnabled($news) || $this->unifiedNewsAliases->isMainNews($news)) {
            return;
        }

        $mainNews = $this->unifiedNewsAliases->getMainNews($news);

        if (null === $mainNews) {
            return;
        }

        $news->preventSaving();

        $news->id = 'clone-'.$newsEntry['id'];
        $news->alias = $mainNews->alias;

        $template->linkHeadline = $this->newsHelper->generateHtmlLink($news->headline, $news);
        $template->more = $this->newsHelper->generateHtmlLink($this->translator->trans('MSC.more', [], 'contao_default'), $news, false, true);
        $template->link = News::generateNewsUrl($news);
    }
}
