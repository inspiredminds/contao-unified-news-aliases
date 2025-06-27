<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\FrontendTemplate;
use Contao\NewsModel;
use InspiredMinds\ContaoUnifiedNewsAliases\ModuleNewsHelper;
use InspiredMinds\ContaoUnifiedNewsAliases\UnifiedNewsAliases;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsHook('parseArticles')]
class AdjustNewsLinkListener
{
    private $newsHelper;

    public function __construct(
        private readonly UnifiedNewsAliases $unifiedNewsAliases,
        private readonly TranslatorInterface $translator,
        private readonly ContentUrlGenerator $contentUrlGenerator,
    ) {
        $this->newsHelper = new ModuleNewsHelper();
    }

    public function __invoke(FrontendTemplate $template, array $newsEntry): void
    {
        $news = NewsModel::findById($newsEntry['id']);

        if (!$this->unifiedNewsAliases->isUnifiedAliasEnabled($news) || $this->unifiedNewsAliases->isMainNews($news)) {
            return;
        }

        if (!$mainNews = $this->unifiedNewsAliases->getMainNews($news)) {
            return;
        }

        $news->preventSaving();

        $news->id = 'clone-'.$newsEntry['id'];
        $news->alias = $mainNews->alias;

        $template->linkHeadline = $this->newsHelper->generateHtmlLink($news->headline, $news);
        $template->more = $this->newsHelper->generateHtmlLink($this->translator->trans('MSC.more', [], 'contao_default'), $news, false, true);

        try {
            $template->link = $this->contentUrlGenerator->generate($news);
        } catch (ExceptionInterface) {
            // noop
        }
    }
}
