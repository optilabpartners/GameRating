<?php
namespace Optilab;

add_action('wp_head', function() {
	echo '<script type="text/javascript">
			var ajaxurl = "' . admin_url('admin-ajax.php') . '";
		</script>';
});