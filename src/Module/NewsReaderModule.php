<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified News Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases\Module;

use Contao\Input;
use Contao\ModuleNewsReader;
use Contao\NewsModel;

class NewsReaderModule extends ModuleNewsReader
{
    protected function compile(): void
    {
        $news = NewsModel::findPublishedByParentAndIdOrAlias(Input::get('items'), $this->news_archives);
    }
}
