<?php
function get_taxonomy_parents($id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array()) {    
	$chain = '';   
	$parent = &get_term($id, $taxonomy);

	if (is_wp_error($parent)) {
		return $parent;
	}

	if ($nicename)    
		$name = $parent -> slug;        
	else    
		$name = $parent -> name;

	if ($parent -> parent && ($parent -> parent != $parent -> term_id) && !in_array($parent -> parent, $visited)) {    
		$visited[] = $parent -> parent;    
		$chain .= get_taxonomy_parents($parent ->parent, $taxonomy, $link, $separator, $nicename, $visited);

	}

	if ($link) {
		// nothing, can't get this working :(
	} else    
		$chain .= $name . $separator;    
	return $chain;    
}