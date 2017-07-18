<?php

add_filter('rewrite_rules_array', 'optilab_rewrite_rules');
function optilab_rewrite_rules($rules) {
    $newRules  = array();
    $newRules['game/(.+)/(.+)/(.+)/(.+)/?$'] = 'index.php?game=$matches[4]'; // my custom structure will always have the post name as the 5th uri segment
    $newRules['game/(.+)/(.+)/(.+)/?$']      = 'index.php?game_season=$matches[3]'; 
    $newRules['game/(.+)/(.+)/?$']           = 'index.php?game_season=$matches[2]'; 
    $newRules['game/(.+)/?$']                = 'index.php?game_org=$matches[1]'; 

    return array_merge($newRules, $rules);
}