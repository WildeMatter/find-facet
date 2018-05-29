<?php
/*
Plugin Name: Matter Solutions - Switchboard In A Box find Facets
Plugin URI: https://www.mttr.io/
Description: Finding And Building Facets
Version: 0.9.2
Author: Matter Solutions / Ben 
Author URI: https://www.mttr.io/about/team/ben-maden
License: Custom
Text Domain: msau
*/

/**
 * ACTHUNG TODO - ROADMAP
 * CRITICAL
 *
 *
 * IMPORTANT
 *
 *
 * NICE TO HAVE
 *
 *
 *
 * DONE
 * - begin plugin
 *
 */


$MTTR_CFG = array(
	'debug'          => true,
	'plugin_path'    => plugin_dir_path( __FILE__ ),
	'plugin_url'     => plugin_dir_url( __FILE__ ),
	'delete_type'    => 'soft',
	'admin_base_url' => './admin.php?page=mttr_sbib_disc_codes-page&',
);

// include some additional functionality
require_once $MTTR_CFG['plugin_path'] . 'includes/_lib.find_facets.php';
require_once $MTTR_CFG['plugin_path'] . 'includes/_sbib.product.class.php';
require_once $MTTR_CFG['plugin_path'] . 'includes/_sbib.facetwp.class.php';


//
add_action( 'init', 'mttr_sbib_find_facets' );
function mttr_sbib_find_facets() {
	global $MTTR_CFG;

	$_get_key      = $_GET['key'] ?? null;
	$_get_facetkey = $_GET['facetkey'] ?? null;
	$_get_action   = $_GET['action'] ?? null;
	$_get_task     = $_GET['task'] ?? null;


	if ( null !== $_get_key && '1xxXXdd3xC' === $_get_key ) {
		echo "<h1>Finding MTTR Facets</h1>\n";

		if ( 'list' === $_get_action ) {
			mttr_sbib_find_facets_list_categories();
		}

		if ( 'drill' === $_get_action ) {

			if ( 'makefacet' === $_get_task && '' !== $_get_facetkey ) {
				mttr_sbib_find_facets_makefacet();
			} elseif ( 'removefacet' === $_get_task ) {
				mttr_sbib_find_facets_removefacet();
			}

			mttr_sbib_find_facets_drill_category();
		}

		if ( 'facetwp' === $_get_action ) {
			mttr_sbib_find_facets_facetwp();
		}

		if ( 'facetwpcompare' === $_get_action ) {
			mttr_sbib_find_facets_facetwpcompare();
		}

		exit;
	}

}
