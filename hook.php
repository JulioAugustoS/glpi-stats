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

function plugin_stats_install(){

    global $DB, $LANG;

    return true;

}

function plugin_stats_uninstall(){

    global $DB;

    return true;

}