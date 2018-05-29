<?php /** @noinspection ALL */

function mttr_sbib_find_facets_drill_category() {
	global $wpdb;
	$deeper = array();
	// https://wordpress.stackexchange.com/questions/139196/display-all-products-by-category-with-woocommerce
	echo '<h3>' . __FUNCTION__ . "</h3>\n";
	$number             = - 1;
	$ids                = array( $_REQUEST['cat'] );
	$hide_empty         = true;
	$args               = array(
		'number'     => $number,
		'orderby'    => 'title',
		'order'      => 'ASC',
		'hide_empty' => $hide_empty,
		'include'    => $ids
	);
	$product_categories = get_terms( 'product_cat', $args );
	$count              = count( $product_categories );
	if ( $count > 0 ) {
		foreach ( $product_categories as $product_category ) {
//			echo '<h4><a target="_blank" href="' . get_term_link( $product_category ) . '">' . $product_category->name . '</a></h4>';
			echo "<h4><a target='_blank' href='{get_term_link( $product_category )}'>{$product_category->name}</a></h4>";

			$args     = array(
				'posts_per_page' => - 1,
				'tax_query'      => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $product_category->slug
					)
				),
				'post_type'      => 'product',
				'orderby'        => 'title,'
			);
			$products = new WP_Query( $args );
			echo '<ul>';
			$first = true;
			foreach ( $products->posts as $product ) {
				// while ( $products->have_posts() ) {
				// $product = $products->the_post();
				$post_meta      = get_post_meta( $product->ID );
				$product_object = new Mttr_Sbib_Product( $product->ID, $product, $post_meta );

				?>
                <li>
                    <a href="<?php
					echo get_permalink( $product );
					?>">
						<?php
						echo $product->post_title;
						echo '</a> ';
						// echo " ";
						echo ' | ';
						echo $product_object->sku;
						echo ' | ';
						if ( ! $product_object->coscode ) {
							echo '<font color=red>NO COSCODE</font>';
						} else {
							echo $product_object->coscode;
						}
						echo ' | ';
						if ( $product_object->is_parsed ) {
							echo 'PARSED: ' . date( 'Y-m-d H:i:s', $product_object->is_parsed );

							if ( isset( $_REQUEST['deeper'] ) && 1 === $_REQUEST['deeper'] ) {
								$meta_clean                                = Mttr_Sbib_Product::translate_to_keyval( $post_meta );
								$deeper['products'][ $product_object->ID ] = $meta_clean;
								$deeper['summary']                         = Mttr_Sbib_Product::collate( $deeper['summary'],
									$meta_clean );
							}

						} else {
							echo 'NOT_PARSED';
						}

						//the_title(); ?>

						<?php
						// foreach($post_meta as $)
						if ( $first ) {
							print '<pre>';
							print_r( $post_meta );
							print '</pre>';
							$first = false;
						}
						?>
                </li>
				<?php
			}
			echo '</ul>';
		}
	}

	if ( isset( $_REQUEST['deeper'] ) && 1 === $_REQUEST['deeper'] ) {
		// print "<pre><font color=purple>";
		Mttr_Sbib_Product::render_deeper( $deeper );
		// print "</font></pre>";
	} else {
		echo 'Want more details? <A href="./?key=' . $_REQUEST['key'] . '&action=drill&cat=' . $_REQUEST['cat'] . '&deeper=1">GO DEEPER</a>';
	}


}

function mttr_sbib_find_facets_list_categories() {

	echo '<h3>' . __FUNCTION__ . "</h3>\n";

	// load categories
	// https://stackoverflow.com/questions/21009516/get-woocommerce-product-categories-from-wordpress
	$taxonomy     = 'product_cat';
	$orderby      = 'name';
	$show_count   = 0;      // 1 for yes, 0 for no
	$pad_counts   = 0;      // 1 for yes, 0 for no
	$hierarchical = 1;      // 1 for yes, 0 for no
	$title        = '';
	$empty        = 1;

	$args           = array(
		'taxonomy'     => $taxonomy,
		'orderby'      => $orderby,
		'show_count'   => $show_count,
		'pad_counts'   => $pad_counts,
		'hierarchical' => $hierarchical,
		'title_li'     => $title,
		'hide_empty'   => $empty
	);
	$all_categories = get_categories( $args );
	// print '<pre>';
	// print '<font color=purple>';
	// print_r($args);
	// print '</font>';
	// print_r($all_categories);
	// print '</pre>';

	echo "<p>Total Categories: " . count( $all_categories ) . "</p>";

	echo "<ol type='1'>\n";
	foreach ( $all_categories as $cat ) {

//	    echo '<pre>';
//        print_r($cat);
//        echo '</pre>';
//        exit;

		if ( $cat->category_parent === 0 ) {

			echo '<li>[' . $cat->term_id . '] <a target="_blank" href="' . get_term_link( $cat->slug,
					'product_cat' ) . '">' . $cat->name . '(' . $cat->count . ')</a>';
			echo ' <a target="_blank" href="./?key=1xxXXdd3xC&action=drill&cat=' . $cat->term_id . '">drill</a>';
			echo '</li>' . "\n";

			// subcats
			$args_sub = array(
				'taxonomy'     => $taxonomy,
				'child_of'     => 0,
				'parent'       => $cat->term_id,
				'orderby'      => $orderby,
				'show_count'   => $show_count,
				'pad_counts'   => $pad_counts,
				'hierarchical' => $hierarchical,
				'title_li'     => $title,
				'hide_empty'   => $empty
			);
			$sub_cats = get_categories( $args_sub );
			if ( $sub_cats ) {
				echo "<ol type='a'>\n";
				foreach ( $sub_cats as $sub_category ) {
					//


					echo '<li>[' . $sub_category->term_id . '] <a target="_blank" href="' . get_term_link( $sub_category->slug,
							'product_cat' ) . '">' . $sub_category->name . '(' . $sub_category->count . ')</a>';
					echo ' <a target="_blank" href="./?key=1xxXXdd3xC&action=drill&cat=' . $sub_category->term_id . '">drill</a>';
					echo '</li>' . "\n";
					// print_r($sub_category);
					// continue;

					// subsubcats
					$args_sub_sub = array(
						'taxonomy'     => $taxonomy,
						'child_of'     => 0,
						'parent'       => $sub_category->term_id,
						'orderby'      => $orderby,
						'show_count'   => $show_count,
						'pad_counts'   => $pad_counts,
						'hierarchical' => $hierarchical,
						'title_li'     => $title,
						'hide_empty'   => $empty
					);
					$sub_sub_cats = get_categories( $args_sub_sub );
					if ( $sub_sub_cats ) {
						echo "<ol type='i'>\n";
						foreach ( $sub_sub_cats as $sub_sub_category ) {
							//
							echo '<li>[' . $sub_sub_category->term_id . '] <a target="_blank" href="' . get_term_link( $sub_sub_category->slug,
									'product_cat' ) . '">' . $sub_sub_category->name . '(' . $sub_sub_category->count . ')</a>';
							echo ' <a target="_blank" href="./?key=1xxXXdd3xC&action=drill&cat=' . $sub_sub_category->term_id . '">drill</a>';
							echo '</li>' . "\n";
						}
						echo "</ol>\n";
					}
					// /subsubcats
				}

				echo "</ol><!-- /sub_cat -->\n";
			}
			// /subcats

		}
	}
	echo "</ol>\n";
}

function mttr_sbib_find_facets_makefacet() {
	$cat      = $_REQUEST['cat'];
	$facetkey = $_REQUEST['facetkey'];
	//
	$fwp = new Mttr_Sbib_Facet_Config();
	$obj = $fwp->make( $facetkey, $cat );
	// print '<pre>';
	// print_r($obj);
	// print '</pre>';
}

function mttr_sbib_find_facets_removefacet() {
	$cat      = $_REQUEST['cat'];
	$facetkey = $_REQUEST['facetkey'];
	//
	$fwp        = new Mttr_Sbib_Facet_Config();
	$facet_name = $fwp->get_facetname( $facetkey, $cat );
	if ( $fwp->remove( $facet_name ) ) {
		echo 'REMOVED: ' . $facet_name . '<br />' . "\n";
	}
}

function mttr_sbib_find_facets_facetwp() {
	$facetwp_settings = get_option( 'facetwp_settings' );
	$arr              = json_decode( $facetwp_settings );
	print '<pre>';
	print_r( $arr );
	print '</pre>';
}

function mttr_sbib_find_facets_facetwpcompare() {
	$fwp = new Mttr_Sbib_Facet_Config();
	print '<pre>';
	$cats = array();
	foreach ( $fwp->settings->facets as $facet ) {
		echo $facet->name . ' ';
		if ( ! preg_match( '|product_cat_([0-9]*)_([0-9]*)|', $facet->name, $matches ) ) {
			continue;
		}
		// print_r($matches);
		$cat_id = $matches[1];
		echo '[' . $cat_id . "]\t";

		if ( ! isset( $cats[ $cat_id ] ) ) {
			$cats[ $cat_id ] = get_term( $cat_id, 'product_cat', 'OBJECT' );
		}
		$cat = $cats[ $cat_id ];
		//
		if ( is_object( $cat ) ) {
			echo "FOUND: <a href='./?key=1xxXXdd3xC&action=drill&cat=" . $cat_id . "'>" . $cat->name . "</a>\n";
		} else {
			echo 'NOT FOUND';
		}
		// ??
		// echo "dhdhd";

		// exit;
	}

}
