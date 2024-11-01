<?php
add_shortcode( 'wp_portfolio', 'wp_portfolio_shorcode' );

function wp_portfolio_shorcode($atts, $content = null) {
	extract( shortcode_atts(
		array(
			'post_no'       => '',
			'category_slug'       => '',
			), $atts )
	);
	ob_start();
	?>


	<div class="portfolio" class="gallery-section">
		<div class="container">

			<div  class="isotope-filters portfolio-filter">

				<button class="button is-checked" data-sort-by="original-order">ALL</button>
				<?php

    			$strings="$category_slug";
				$str = str_replace(',', '', $strings);
				$array=explode(" ",$str);
				$args = array(
					'slug'           => $array, 
					); 
				if(!empty($category_slug)) {
					$filters = get_terms( 'portfolio-category', $args );
				} else {
					$filters = get_terms( 'portfolio-category');
				}
				foreach ($filters as $filter) {
					echo "<button data-filter=\".$filter->slug\">$filter->name</button>";
				}
				?>

				<?php 
			if(!empty($category_slug)) {
				global $post;
				$paged = get_query_var('paged') ? get_query_var('paged') : 1;
				$args = array(
					'post_type' => 'portfolio',
					'posts_per_page' => esc_attr($post_no),
					'paged' => get_query_var('paged'),
					'tax_query' => array(
						array(
							'taxonomy' => 'portfolio-category',
							'field' => 'slug',
							'terms' => $array,
							)
						)
					);
				$query = new WP_Query( $args );
			} else {
				global $post;
				$args = array(
					'post_type' => 'portfolio',
					'posts_per_page' => esc_attr($post_no),
					'paged' => get_query_var('paged'),
					'taxonomy' => 'portfolio-category',

					);
				$query = new WP_Query( $args );
			}
			?>
			</div>
		</div>
		<div class="clearfix portfolio-item isotope-items isotope-masonry-items">
			<?php 
			if($query->have_posts()) : while($query->have_posts()) : $query->the_post(); 

			$terms = wp_get_post_terms(get_the_ID(), 'portfolio-category' ); 
			$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );


			$t = array();       
			foreach($terms as $term) 
				$t[] = $term->slug;
			?> 
			<div class="item <?php echo implode(' ', $t); ?> effect-oscar">
				<?php if ( has_post_thumbnail() ) { 
					the_post_thumbnail('medium'); 
				} else {
					echo '<img src="'.get_template_directory_uri().'/assets/images/no-img.jpg">';
				}
				?>

				<figcaption class="item-description">
					<h2 class="item-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2><!-- /.item-title -->
				</figcaption>
			</div>
			<?php 
			endwhile;endif;

                              //reset
			wp_reset_query();

			?>
		</div><!-- /.gallery-item -->

		<div class="clearfix"></div>
	</div><!-- /.gallery-section -->

	<?php
	return ob_get_clean();
}