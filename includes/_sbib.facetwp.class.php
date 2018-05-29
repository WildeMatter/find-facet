<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection AutoloadingIssuesInspection */

class Mttr_Sbib_Facet_Config {

	public $settings;

	public function __construct() {
		$this->settings = json_decode( get_option( 'facetwp_settings' ) );
	}

	/**
	 * @return bool
	 */
	public function save() {
		$str = json_encode( $this->settings );

		return update_option( 'facetwp_settings', $str );
	}

	public function exists( $key ) {
		foreach ( $this->settings->facets as $facet ) {
			if ( preg_match( '|' . $key . '|', $facet->source ) ) {
				return true;
			}
		}

		return false;
	}

	public function get_facetname( $key, $cat ) {
		foreach ( $this->settings->facets as $facet ) {
			if ( preg_match( '|' . $key . '|', $facet->source ) && preg_match( '|product_cat_' . $cat . '_|', $facet->name ) ) {
				return $facet->name;
			}
		}

		return false;
	}


	/**
	 * @param $label
	 *
	 * @return bool
	 */
	public function facet_label_exists( $label ) {
		foreach ( $this->settings->facets as $facet ) {
			if ( $label === $facet->label ) {
				return true;
			}
		}

		return false;
	}


	public function make( $key, $cat ) {
		$obj = new StdClass();
		for ( $i = 0; $i < 1000; $i ++ ) {
			$label = 'product_cat-' . $cat . '_' . $i;
			if ( ! $this->facet_label_exists( $label ) ) {
				break;
			}
		}
		$obj->label           = $label;
		$obj->name            = str_replace( '-', '_', $label );
		$obj->type            = 'checkboxes';
		$obj->source          = 'cf/' . $key;
		$obj->parent_term     = false;
		$obj->orderby         = 'count';
		$obj->operator        = 'and';
		$obj->hierarchical    = 'no';
		$obj->show_expanded   = 'no';
		$obj->ghosts          = 'yes';
		$obj->preserve_ghosts = 'yes';
		$obj->count           = 10;
		$obj->soft_limit      = 5;

		$this->settings->facets[] = $obj;
		$this->save();

		return $obj;
	}

	public function remove( $name ) {
		$facets_to_keep = array();
		foreach ( $this->settings->facets as $facet ) {
			if ( $name === $facet->name ) {
				continue;
			}
			$facets_to_keep[] = $facet;
		}
		$this->settings->facets = $facets_to_keep;

		return $this->save();
	}

}