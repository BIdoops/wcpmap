<?php

/**
 * The template for displaying demo plugin content.
 *
 *
 * @author 		WCPetroMap
 * @package 	WCPMap/Templates
 * @version     1.0.0
 */

extract($instance);
global $WCMp;

?>
<div class="wcmp-store-location-wrapper">
	<?php
	if (!empty($store_lat) && !empty($store_lng)) : ?>
		<div id="store-maps" class="store-maps" class="wcmp-gmap" style="max-height: 200px; height: 200px; position: relative;"></div>
	<?php


	endif; ?>
	<a href="<?php echo $pmap_link ?>" target="_blank"><?php echo 'Mostrar en PetroMap' ?></a>
</div>