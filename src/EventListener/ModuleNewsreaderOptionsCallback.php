<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Doctrine\DBAL\Connection;

#[AsCallback('tl_module', 'fields.news_readerModule.options', priority: 100)]
class ModuleNewsreaderOptionsCallback
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function __invoke(): array
    {
        $options = [];
        $modules = $this->db->fetchAllAssociative("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type LIKE 'newsreader%' ORDER BY t.name, m.name");

        foreach ($modules as $module) {
            $options[$module['theme']][$module['id']] = $module['name'].' (ID '.$module['id'].')';
        }

        return $options;
    }
}
