$(document).ready(function() {

    var ajaxBaseUrl = '../plugins/stats/ajax'
    var path = document.location.pathname

    if(path.indexOf('plugins/') !== -1){
        var pluginPath = path.substring(path.indexOf('plugins'))
        var nbDirectory = (pluginPath.match(/\//g) || []).length + 1;
        var ajaxBaseUrl = Array(nbDirectory).join("../") + 'plugins/stats/ajax'
    }

    var pluginStatsDisplay = function(){
        $("#page").prepend("<tr><td colspan='1' id='stats_inserted'></td></tr>")
        $("#stats_inserted").load(ajaxBaseUrl + "/display_stats.php")
    }

    if(window.location.href.indexOf("central.php") > 0){
        pluginStatsDisplay()
    }

})