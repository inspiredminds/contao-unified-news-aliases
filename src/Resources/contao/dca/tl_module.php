<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified News Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use InspiredMinds\ContaoUnifiedNewsAliases\Controller\FrontendModule\NewsReaderModuleController;

$GLOBALS['TL_DCA']['tl_module']['palettes'][NewsReaderModuleController::TYPE] = $GLOBALS['TL_DCA']['tl_module']['palettes']['newsreader'];
