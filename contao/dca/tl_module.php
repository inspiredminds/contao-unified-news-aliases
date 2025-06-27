<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

use InspiredMinds\ContaoUnifiedNewsAliases\Controller\FrontendModule\NewsReaderModuleController;

$GLOBALS['TL_DCA']['tl_module']['palettes'][NewsReaderModuleController::TYPE] = $GLOBALS['TL_DCA']['tl_module']['palettes']['newsreader'];
