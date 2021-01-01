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

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Doctrine\DBAL\Connection;

/**
 * @Callback(table="tl_module", target="fields.news_readerModule.options", priority=100)
 */
class ModuleNewsreaderOptionsCallback
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function __invoke(): array
    {
        $options = [];
        $modules = $this->db
            ->executeQuery("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type LIKE 'newsreader%' ORDER BY t.name, m.name")
            ->fetchAll()
        ;

        if (false === $modules) {
            return $options;
        }

        foreach ($modules as $module) {
            $options[$module['theme']][$module['id']] = $module['name'].' (ID '.$module['id'].')';
        }

        return $options;
    }
}
