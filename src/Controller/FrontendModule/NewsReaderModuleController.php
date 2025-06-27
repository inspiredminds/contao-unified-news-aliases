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

use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;
use Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor;
use Contao\Input;
use Contao\ModuleModel;
use Contao\ModuleNewsReader;
use Contao\NewsModel;
use InspiredMinds\ContaoUnifiedNewsAliases\UnifiedNewsAliases;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsFrontendModule(self::TYPE, 'news', 'mod_newsreader')]
class NewsReaderModuleController extends ModuleNewsReader
{
    public const TYPE = 'newsreader_unified_aliases';

    public function __construct(
        private readonly UnifiedNewsAliases $unifiedAliases,
        private readonly ContentUrlGenerator $contentUrlGenerator,
        private readonly ResponseContextAccessor $responseContextAccessor,
    ) {
    }

    public function __invoke(ModuleModel $model, string $section): Response
    {
        parent::__construct($model, $section);

        return new Response($this->generate());
    }

    protected function compile(): void
    {
        $override = $this->override();

        parent::compile();

        // Override the canonical URI as well, if applicable
        if ($override) {
            $responseContext = $this->responseContextAccessor->getResponseContext();

            if ($responseContext?->has(HtmlHeadBag::class) && !$override->canonicalLink && !$this->news_keepCanonical) {
                $responseContext->get(HtmlHeadBag::class)
                    ->setCanonicalUri($this->contentUrlGenerator->generate($override, [], UrlGeneratorInterface::ABSOLUTE_URL))
                ;
            }
        }
    }

    private function override(): NewsModel|null
    {
        // Check if this is a valid news alias
        if (!$news = NewsModel::findOneByAlias(Input::get('auto_item', false, true))) {
            return null;
        }

        // Check if unified aliases feature is enabled for this news
        if (!$this->unifiedAliases->isUnifiedAliasEnabled($news)) {
            return null;
        }

        // Get the actual news for the current language
        if (!$actualNews = $this->unifiedAliases->getNewsForCurrentLanguage($news)) {
            return null;
        }

        // Check if this news is actually allowed here
        if (!NewsModel::findPublishedByParentAndIdOrAlias($actualNews->alias, $this->news_archives)) {
            return null;
        }

        // Redirect in case the detail URL was accessed with the regular alias of the news
        if ($this->unifiedAliases->isCurrentLanguage($news) && !$this->unifiedAliases->isMainNews($news)) {
            $this->redirectToMainNewsUrl($news);
        }

        // Override the "items" variable
        Input::setGet('auto_item', $actualNews->alias);

        return $news;
    }

    private function redirectToMainNewsUrl(NewsModel $news): void
    {
        if (!$mainNews = $this->unifiedAliases->getMainNews($news)) {
            return;
        }

        $news->preventSaving();

        $news->id = 'clone-'.$news->id;
        $news->alias = $mainNews->alias;

        throw new RedirectResponseException($this->contentUrlGenerator->generate($news, [], UrlGeneratorInterface::ABSOLUTE_URL), Response::HTTP_MOVED_PERMANENTLY);
    }
}
