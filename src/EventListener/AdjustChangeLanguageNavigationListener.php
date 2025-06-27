<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Input;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use InspiredMinds\ContaoUnifiedNewsAliases\UnifiedNewsAliases;
use Terminal42\ChangeLanguage\Event\ChangelanguageNavigationEvent;

#[AsHook('changelanguageNavigation', priority: -100)]
class AdjustChangeLanguageNavigationListener
{
    public function __construct(private readonly UnifiedNewsAliases $unifiedAliases)
    {
    }

    public function __invoke(ChangelanguageNavigationEvent $event): void
    {
        if (!$alias = Input::get('auto_item', false, true)) {
            return;
        }

        global $objPage;

        // Check if any news archive have this page as its target page
        if (!$archives = NewsArchiveModel::findBy('jumpTo', $objPage->id)) {
            return;
        }

        // Check if unified aliases feature is enabled for this news
        if (!$currentNews = NewsModel::findOneByAlias($alias)) {
            return;
        }

        if (!$this->unifiedAliases->isUnifiedAliasEnabled($currentNews)) {
            return;
        }

        // Get the actual news for the current language
        if (!$actualNews = $this->unifiedAliases->getNewsForCurrentLanguage($currentNews)) {
            return;
        }

        // Check if this news is actually allowed here
        if (!\in_array((int) $actualNews->pid, array_map('intval', $archives->fetchEach('id')), true)) {
            return;
        }

        // Get the main news for the current news
        $mainNews = $this->unifiedAliases->getMainNews($currentNews);

        if (!$mainNews) {
            if (!$this->unifiedAliases->isMainNews($currentNews)) {
                return;
            }

            $mainNews = $currentNews;
        }

        // Override the "items" URL attribute with the alias of the main news
        $event->getUrlParameterBag()->setUrlAttribute('auto_item', $mainNews->alias);
    }
}
