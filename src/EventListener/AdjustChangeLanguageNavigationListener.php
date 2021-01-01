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
use Contao\Input;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use InspiredMinds\ContaoUnifiedNewsAliases\UnifiedNewsAliases;
use Terminal42\ChangeLanguage\Event\ChangelanguageNavigationEvent;

/**
 * @Hook("changelanguageNavigation", priority=-100)
 */
class AdjustChangeLanguageNavigationListener
{
    private $unifiedAliases;

    public function __construct(UnifiedNewsAliases $unifiedAliases)
    {
        $this->unifiedAliases = $unifiedAliases;
    }

    public function __invoke(ChangelanguageNavigationEvent $event): void
    {
        $alias = Input::get('auto_item', false, true);

        if (empty($alias)) {
            return;
        }

        global $objPage;

        $archives = NewsArchiveModel::findBy('jumpTo', $objPage->id);

        // Check if any news archive have this page as its target page
        if (null === $archives) {
            return;
        }

        // Check if unified aliases feature is enabled for this news
        $currentNews = NewsModel::findOneByAlias($alias);

        if (null === $currentNews) {
            return;
        }

        if (!$this->unifiedAliases->isUnifiedAliasEnabled($currentNews)) {
            return;
        }

        // Get the actual news for the current language
        $actualNews = $this->unifiedAliases->getNewsForCurrentLanguage($currentNews);

        if (null === $actualNews) {
            return;
        }

        // Check if this news is actually allowed here
        if (!\in_array((int) $actualNews->pid, array_map('intval', $archives->fetchEach('id')), true)) {
            return;
        }

        // Get the main news for the current news
        $mainNews = $this->unifiedAliases->getMainNews($currentNews);

        if (null === $mainNews) {
            if (!$this->unifiedAliases->isMainNews($currentNews)) {
                return;
            }

            $mainNews = $currentNews;
        }

        // Override the "items" URL attribute with the alias of the main news
        $event->getUrlParameterBag()->setUrlAttribute('items', $mainNews->alias);
    }
}
