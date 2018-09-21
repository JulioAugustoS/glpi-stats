<?php

/*
   ------------------------------------------------------------------------
   Projects
   Copyright (C) 2018 by the Stats Development Team.

   https://github.com/JulioAugustoS/stats
   ------------------------------------------------------------------------

   LICENSE

   This file is part of Stats project.

   Stats plugin is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Stats plugin is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with Stats plugin. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   Stats plugin
   @copyright Copyright (c) 2018 Stats team
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @link      https://github.com/JulioAugustoS/stats
   @since     2018

   ------------------------------------------------------------------------
 */

define("PLUGIN_STATS_VERSION", "1.0.0");

function plugin_init_stats(){

    global $PLUGIN_HOOKS, $LANG;

    $PLUGIN_HOOKS['csrf_compliant']['stats'] = true;

    $Plugin = new Plugin();
    if($Plugin->isInstalled('stats') && $Plugin->isActivated('stats')){

        Plugin::registerClass('PluginStats', [
            'addtabon' => ['Config']
        ]);

        $PLUGIN_HOOKS['config_page']['stats'] = 'config.php';
        $PLUGIN_HOOKS['add_javascript']['stats'] = "scripts/stats.js";

    }

}

function plugin_version_stats(){

    global $DB, $LANG;

    return [
        'name'              => _n("GLPI Stats", "GLPI Stats", "stats"),
        'minGlpiVersion'    => '9.3.0',
        'version'           => PLUGIN_STATS_VERSION,
        'author'            => '<a href="mailto:julioaugustoms@gmail.com"> Julio Augusto </a>',
        'license'           => 'GPLv3+',
        'homepage'          => 'https://github.com/JulioAugustoS/stats',
        'minGlpiVersion'    => '9.3'
    ];

}

function plugin_stats_check_prerequisities(){

    if(version_compare(GLPI_VERSION, '9.3', 'lt') || version_compare(GLPI_VERSION, '9.4', 'ge')){
        
        echo __("This plugin requires GLPI >= 9.3");
        return false;

    }

    return true;

}

function plugin_stats_check_config($verbose = false){

    if(true){
        return true;
    }

    if($verbose){
        echo __("Installed / not configured", 'stats');
    }

    return true;

}