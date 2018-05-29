<?php

class Mttr_Sbib_Product {

	public $ID, $data, $meta;
	public $is_parsed;
	public $coscode;
	public $sku;

	public function __construct( $ID, $data = false, $meta = false ) {
		$this->ID = $ID;
		if ( ! $data ) {
			die( "No Data Supplied! " . __CLASS__ . "\n" );
		}
		if ( ! $meta ) {
			$meta = get_post_meta( $this->ID );
		}
		$this->data = $data;
		$this->meta = $meta;
		//
		$this->init();
	}

	public function init() {
		$this->sku     = array_shift( $this->meta['_sku'] );
		$this->coscode = array_shift( $this->meta['coscode'] );
		if ( $this->meta['main_parsed'] ) {
			$this->is_parsed = array_shift( $this->meta['main_parsed'] );
		} else {
			$this->is_parsed = false;
		}
	}

	/**
	 * @param $arr
	 *
	 * @return array
	 */
	public static function translate_to_keyval( $arr ) {
		$clean   = array();
		$skip_it = array(
			'_wc_review_count',
			'_wc_rating_count',
			'_wc_average_rating',
			'_sku',
			'_regular_price',
			'_sale_price',
			'_sale_price_dates_from',
			'_sale_price_dates_to',
			'total_sales',
			'_tax_status',
			'_tax_class',
			'_manage_stock',
			'_backorders',
			'_sold_individually',
			'_weight',
			'_length',
			'_width',
			'_height',
			'_upsell_ids',
			'_crosssell_ids',
			'_purchase_note',
			'_default_attributes',
			'_virtual',
			'_downloadable',
			'_product_image_gallery',
			'_download_limit',
			'_download_expiry',
			'_thumbnail_id',
			'_stock',
			'_stock_status',
			'_downloadable_files',
			'_product_attributes',
			'_product_version',
			'_price',
			'complementary_parsed',
			'main_parsed',
			'reference_parsed',
		);

		print '<pre>';
		$post_id = 36789;
		echo $post_id . "\n";
		if ( update_post_meta( $post_id, 'testing_x_x', 'value1', 'blob' ) ) {
			echo "1";
		}
		if ( update_post_meta( $post_id, 'testing_x_x', 'value2', 'blob' ) ) {
			echo "2";
		}
		if ( update_post_meta( $post_id, 'testing_x_x', 'value3', 'blob' ) ) {
			echo "3";
		}

		print '<font color=purple>';
		print_r( $arr );
//		exit;
		foreach ( $arr as $key => $val ) {
			if ( in_array( $key, $skip_it, true ) ) {
				continue;
			}
			$array_test = maybe_unserialize( $val );
			// if( is_array($array_test) ) $val = $array_test;
			if ( is_array( $val ) && count( $val ) === 1 ) {
				$clean[ $key ] = array_shift( $val );
				// } else if( is_array($val) && count($val) > 1 ) {
				// $clean[$key] = $val;
			} else {
				$clean[ $key ] = $val;
			}
		}

		return $clean;
	}

	public static function collate( $summary, $meta_clean ) {
		// how many in this summary?
		if ( ! $summary['count'] ) {
			$summary['count'] = 0;
		}
		$summary['count'] ++;
		//
		// fields from the meta?
		foreach ( $meta_clean as $key => $value ) {
			// does it have new keys?
			if ( ! isset( $summary[ $key ] ) ) {
				$summary[ $key ] = array(
					'count'   => 0,
					'options' => array(),
				);
				// array count and vals
			}
			$value = trim( $value );
			if ( '' !== $value ) {
				$summary[ $key ]['count'] ++;
				if ( ! isset( $summary[ $key ]['options'][ $value ] ) ) {
					$summary[ $key ]['options'][ $value ] = 1;
				} else {
					$summary[ $key ]['options'][ $value ] ++;
				}
			}
		}

		// $meta_clean = self::translate_to_keyval($meta);
		return $summary;
	}


	private static function _get_coscode_from_summary( $summary ) {
		$coscode_row = $summary['coscode'];
		foreach ( $coscode_row['options'] as $key => $val ) {
			return $key;
		}

		return false;
	}


	public static function render_deeper( $deeper ) {
		$summary = $deeper['summary'];
		$fwp     = new Mttr_Sbib_Facet_Config();
		// print '<pre>';
		// print_r($fwp);
		// print '</pre>';

		echo "<h3>Deeper look at this category</h3>\n";
		echo "<h4>Count: " . $summary['count'];
		echo " & Coscode = " . self::_get_coscode_from_summary( $summary ) . "</h4>\n";

		echo "<table border='1'>\n<tr>\n";
		echo "<th>Key</th><th>Count</th><th>Options</th><th></th>\n";
		echo "</tr>\n";

		foreach ( $summary as $key => $val ) {
			if ( 'count' === $key ) {
				continue;
			}
			if ( 'coscode' === $key ) {
				continue;
			}
			if ( '_edit_lock' === $key ) {
				continue;
			}

			echo "<tr>\n";
			echo "<td>" . $key;
			$facet_name = $fwp->get_facetname( $key, $_REQUEST['cat'] );
			if ( $facet_name ) {
				echo "<br /><span style='font-size:10px'>Facet: " . $facet_name . "</span>";
			}
			echo "</td>\n";
			echo "<td>" . $val['count'] . "</td>\n";
			echo "<td><pre>";
			print_r( $val['options'] );
			echo "</pre></td>\n";
			echo "<td>";
			// 
			// echo $facet_name;
			if ( $facet_name ) {
				echo '<a target="_blank" href="' . admin_url( 'options-general.php?page=facetwp' ) . '">View</a>';
				echo "<br />\n";
				echo '<a href="./?key=1xxXXdd3xC&action=' . $_REQUEST['action'] . '&cat=' . $_REQUEST['cat'] . '&deeper=1&task=removefacet&facetkey=' . $key . '"><!-- are you sure -->Remove</a>';
			} else {
				echo '<a target="_blank" href="./?key=1xxXXdd3xC&action=' . $_REQUEST['action'] . '&cat=' . $_REQUEST['cat'] . '&deeper=1&task=makefacet&facetkey=' . $key . '">Make</a>';
			}
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo '</table>';
	}


}

