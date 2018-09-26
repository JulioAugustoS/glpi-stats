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

if(!defined('GLPI_ROOT')):
    die("Sorry. You can't access directly to this file");
endif;

if(!defined("GLPI_STATS_DIR")):
    define("GLPI_STATS_DIR", GLPI_ROOT . "/plugins/stats");
endif;

class PluginStats extends CommonDBTM {

    static function groupUsers($userId){

        global $DB;

        $sqlUser = "SELECT DISTINCT `glpi_groups`.`id` AS `Group`
                    FROM `glpi_groups`
                    LEFT JOIN `glpi_groups_users`
                        ON (`glpi_groups_users`.`groups_id` = `glpi_groups`.`id`)
                    WHERE `glpi_groups_users`.`users_id` = '$userId'
                    ";
        $resultUser = $DB->query($sqlUser) or die('Erro ao buscar os grupos do usuario');
        $resultGroups = $DB->fetch_assoc($resultUser);

        return $resultGroups['Group'];

    }

    static function countTickets($status, $group){

        global $DB;

        $sql = "SELECT COUNT(a.id) AS ID
                FROM glpi_tickets a 
                    LEFT JOIN glpi_entities b ON (a.entities_id = b.id)
                    LEFT JOIN glpi_tickets_users c ON (a.id = c.tickets_id AND c.type = 1)
                    LEFT JOIN glpi_users d ON (c.users_id = d.id)
                    LEFT JOIN glpi_itilcategories p ON (a.itilcategories_id = p.id)
                    LEFT JOIN glpi_tickets_users f ON (a.id = f.tickets_id AND f.type = 2)
                    LEFT JOIN glpi_users g ON (f.users_id = g.id)
                    LEFT JOIN glpi_groups_tickets h ON (a.id = h.tickets_id AND h.type = 2)
                    LEFT JOIN glpi_groups i ON (h.groups_id = i.id)
                WHERE a.is_deleted = 0 AND (a.status IN ($status) AND (i.id = '$group'))";
        $result = $DB->query($sql) or die('Erro ao retornar os dados');
        $total = $DB->result($result, 0, 'ID');

        return $total;

    }

    static function countTiketsAtrasados($grupo){

        global $DB;

        $sqlDue = "SELECT COUNT(a.id) AS due
                    FROM glpi_tickets a
                    LEFT JOIN glpi_groups_tickets b ON (b.tickets_id = a.id)
                    WHERE a.status NOT IN (4,5,6) 
                    AND a.is_deleted = 0
                    AND a.time_to_resolve IS NOT NULL
                    AND a.time_to_resolve < NOW()
                    AND b.groups_id = '$grupo'
                ";
        $resultDue = $DB->query($sqlDue) or die('Erro ao retornar o total de chamados');
        $totalDue = $DB->result($resultDue, 0, 'due');

        return $totalDue;

    }

    static function displayStats(){

        global $CFG_GLPI, $DB;

        $userId         = $_SESSION['glpiID'];
        $profileId      = $_SESSION['glpiactiveprofile']['id'];
        $activeEntity   = $_SESSION['glpiactive_entity'];
        $glpiEntity     = $_SESSION['glpiactiveentities'];

        $colorTot = "color: #337AB7";
        $colorNew = "color: #333";
        $colorFec = "color: #555";
        $colorPro = "color: #49BF8F";
        $colorSol = "color: #000";
        $colorPen = "color: #FFA830";
        $colorDue = "color: #D9534F";

        $queryProfile = "SELECT id, name FROM glpi_profiles WHERE interface <> 'helpdesk'";
        $resProfile = $DB->query($queryProfile);

        while($row = $DB->fetch_assoc($resProfile)):
            $arrPro[] = $row['id'];
        endwhile;

        // Retorna o grupo principal do usuario
        $grupoUserId = PluginStats::groupUsers($userId);

        // Total Geral
        $totalGeral = PluginStats::countTickets('1,2,3,4', $grupoUserId);
        
        // Total Novos
        $totalNew = PluginStats::countTickets('1', $grupoUserId);

        // Total Fechados
        $totalClosed = PluginStats::countTickets('6', $grupoUserId);

        // Total Processando
        $totalPro = PluginStats::countTickets('2,3', $grupoUserId);

        // Total Solucionado
        $totalSol = PluginStats::countTickets('5', $grupoUserId);

        // Total Pendentes
        $totalPen = PluginStats::countTickets('4', $grupoUserId);

        // Total Atrasados
        $totalDue = PluginStats::countTiketsAtrasados($grupoUserId);

        //links para lista de chamados
        $href_cham = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&as_map=0&criteria%5B0%5D%5Bfield%5D=12&criteria%5B0%5D%5Bsearchtype%5D=equals&criteria%5B0%5D%5Bvalue%5D=notclosed&criteria%5B1%5D%5Blink%5D=AND&criteria%5B1%5D%5Bfield%5D=8&criteria%5B1%5D%5Bsearchtype%5D=equals&criteria%5B1%5D%5Bvalue%5D=mygroups&search=Pesquisar&itemtype=Ticket&start=0";
        $href_new  = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&as_map=0&criteria%5B0%5D%5Bfield%5D=12&criteria%5B0%5D%5Bsearchtype%5D=equals&criteria%5B0%5D%5Bvalue%5D=1&criteria%5B1%5D%5Blink%5D=AND&criteria%5B1%5D%5Bfield%5D=8&criteria%5B1%5D%5Bsearchtype%5D=equals&criteria%5B1%5D%5Bvalue%5D=mygroups&search=Pesquisar&itemtype=Ticket&start=0";
        $href_clos  = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&as_map=0&criteria%5B0%5D%5Bfield%5D=12&criteria%5B0%5D%5Bsearchtype%5D=equals&criteria%5B0%5D%5Bvalue%5D=6&criteria%5B1%5D%5Blink%5D=AND&criteria%5B1%5D%5Bfield%5D=8&criteria%5B1%5D%5Bsearchtype%5D=equals&criteria%5B1%5D%5Bvalue%5D=mygroups&search=Pesquisar&itemtype=Ticket&start=0";
        $href_pro  = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&as_map=0&criteria%5B0%5D%5Bfield%5D=12&criteria%5B0%5D%5Bsearchtype%5D=equals&criteria%5B0%5D%5Bvalue%5D=process&criteria%5B1%5D%5Blink%5D=AND&criteria%5B1%5D%5Bfield%5D=8&criteria%5B1%5D%5Bsearchtype%5D=equals&criteria%5B1%5D%5Bvalue%5D=mygroups&search=Pesquisar&itemtype=Ticket&start=0";
        $href_solv = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&as_map=0&criteria%5B0%5D%5Bfield%5D=12&criteria%5B0%5D%5Bsearchtype%5D=equals&criteria%5B0%5D%5Bvalue%5D=5&criteria%5B1%5D%5Blink%5D=AND&criteria%5B1%5D%5Bfield%5D=8&criteria%5B1%5D%5Bsearchtype%5D=equals&criteria%5B1%5D%5Bvalue%5D=mygroups&search=Pesquisar&itemtype=Ticket&start=0";
        $href_pend = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&as_map=0&criteria%5B0%5D%5Bfield%5D=12&criteria%5B0%5D%5Bsearchtype%5D=equals&criteria%5B0%5D%5Bvalue%5D=4&criteria%5B1%5D%5Blink%5D=AND&criteria%5B1%5D%5Bfield%5D=8&criteria%5B1%5D%5Bsearchtype%5D=equals&criteria%5B1%5D%5Bvalue%5D=mygroups&search=Pesquisar&itemtype=Ticket&start=0";
        $href_due  = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&as_map=0&criteria%5B0%5D%5Bfield%5D=82&criteria%5B0%5D%5Bsearchtype%5D=equals&criteria%5B0%5D%5Bvalue%5D=1&criteria%5B1%5D%5Blink%5D=AND&criteria%5B1%5D%5Bfield%5D=12&criteria%5B1%5D%5Bsearchtype%5D=equals&criteria%5B1%5D%5Bvalue%5D=notold&criteria%5B2%5D%5Blink%5D=AND&criteria%5B2%5D%5Bfield%5D=8&criteria%5B2%5D%5Bsearchtype%5D=equals&criteria%5B2%5D%5Bvalue%5D=mygroups&search=Pesquisar&itemtype=Ticket&start=0";

        echo '<style>
                #tab_stats {
                    font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; 
                    text-align:center; 
                    margin:auto; 
                    width:90%; 
                    margin-bottom:20px; 
                    background:#fff; 
                    border: 1px solid #ddd; 
                    table-layout: fixed;
                }    
                #tab_stats tr td {
                    padding: 20px;
                }    
                .border {
                    border-right: 1px solid #ddd;
                }
                #tab_stats tr td a {
                    font-size:22pt;
                }
                @media screen and (max-width: 767px){
                    #tab_stats {display:none;}
                }
              </style>';
        echo '<table id="tab_stats">';
        echo '<tr>';
        echo '<td class="border"><span><a style="'.$colorTot.'" href="'.$href_cham.'">' . $totalGeral . '</a> </span> </p><span style="color:#333; font-size:14pt;"> '. _nx('ticket','Opened','Opened',2) . '</span></td>';
        echo '<td class="border"><span><a style="'.$colorNew.'" href="'.$href_new.'">' . $totalNew . '</a> </span> </p><span style="color:#333; font-size:14pt;"> '. Ticket::getStatus(1) .' </span></td>';
        echo '<td class="border"><span><a style="'.$colorFec.'" href="'.$href_clos.'">' . $totalClosed . '</a> </span> </p><span style="color:#333; font-size:14pt;"> '. Ticket::getStatus(6) .' </span></td>';
        echo '<td class="border"><span><a style="'.$colorPro.'" href="'.$href_pro.'">' . $totalPro . '</a></span> </p><span style="color:#333; font-size:14pt;"> '. __('Processing') . ' </span></td>';
        echo '<td class="border"><span><a style="'.$colorSol.'" href="'.$href_solv.'">' . $totalSol . '</a></span> </p><span style="color:#333; font-size:14pt;"> '. Ticket::getStatus(5) .'</span></td>';
        echo '<td class="border"><span><a style="'.$colorPen.'" href="'.$href_pend.'">' . $totalPen . '</a> </span> </p><span style="color:#333; font-size:14pt;"> '. Ticket::getStatus(4) .' </span></td>';
        echo '<td><span><a style="'.$colorDue.'" href="'.$href_due.'">' . $totalDue . '</a>  </span> </p><span style="color:#333; font-size:14pt;"> '. __('Late') . ' </span></td>';
        echo '</tr>';
        echo '</table>';

    }

}