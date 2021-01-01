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

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use InspiredMinds\ContaoUnifiedNewsAliases\Controller\FrontendModule\NewsReaderModuleController;

/**
 * @Callback(table="tl_module", target="fields.customTpl.options", priority=100)
 */
class ModuleCustomTplOptionsCallback
{
    public function __invoke(DataContainer $dc): array
    {
        $type = $dc->activeRecord->type;

        if (NewsReaderModuleController::TYPE === $type) {
            $type = 'newsreader';
        }

        return Controller::getTemplateGroup('mod_'.$type.'_', [], 'mod_'.$type);
    }
}
