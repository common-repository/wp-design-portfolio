<?php
/*
Plugin Name: WP Design Portfolio
Plugin URI: https://www.codenod.com
Description: Wordpress Portfolio is a portfolio plugin. Its simple, easy to use, seo optimze. Category filter available.
Plugin URI: http://htmlmate.com/wp-portfolio
Author: codenod.com
Author URI: https://www.codenod.com
Version: 1.0.2
Requires at least: 4.8
Requires PHP: 7.0
Tested up to: 5.5.1
License: GPL2
Text Domain: wpdp
*/

/*

    Copyright (C) 2015  Jahirul Islam Mamun  mamun3d at gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/*------------------------------------------------------------------------------------------------------------------*/
/*	  The Class.
/*------------------------------------------------------------------------------------------------------------------*/ 

if ( ! class_exists( 'Xtl_Wp_Portfolio' ) ) {

	class Xtl_Wp_Portfolio {

    /**
     * @var string
     */
    public $plugin_url;

    /**
     * @var string
     */
    public $plugin_path;


    /**
    * Hook into the appropriate actions when the class is constructed.
    */
    public function __construct() {

    	define( 'XTL_P_URL', $this->plugin_url() );
    	define( 'XTL_P_DIR', dirname( __FILE__ ) ); 

      //include front end player
    	$this-> xtl_shortcode_include();

      //enqueue script
    	add_action( 'wp_enqueue_scripts', array($this, '_script_loaded') );


    	// custom function load on footer
    	add_action( 'wp_footer', array($this, '_script_load'), 100 );

    	 //custom post type
    	add_action( 'init', array($this, '_custom_post')  );

    	// add_action( 'add_meta_boxes', array( $this, 'xtl_add_meta_box' ) );

    	// add_action( 'save_post', array( $this, 'xtl_save_the_value' ) );
    }

     /**  Include backend js file.
     --------------------------------------------------------------------------------------------------- */

     public function _script_loaded() {

     	//css include
     	wp_enqueue_style( 'custom', XTL_P_URL . '/css/custom.min.css' );

		// js include
     	if(!wp_enqueue_script('jquery' )) {
     		wp_enqueue_script('jquery');
     	}
     	wp_enqueue_script('wpdp-isotop', XTL_P_URL . '/js/plugin.js', 'jQuery', '1.0', true);


     }

    /**  css and js load on footer.
    --------------------------------------------------------------------------------------------------- */

    public function _script_load() {
    	?>
    	<script>

    		/*-------------------------- Isotope Init --------------------*/
    		jQuery(document).ready(function($) {
    			jQuery(window).on("load resize",function(e){

    				var $container = jQuery('.isotope-items'),
    				colWidth = function () {
    					var w = $container.width(), 
    					columnNum = 1,
    					columnWidth = 0;
    					if (w > 1040)     { columnNum  = 5; }  
    					else if (w > 850) { columnNum  = 3; }  
    					else if (w > 768) { columnNum  = 2; }  
    					else if (w > 480) { columnNum  = 2; }
    					columnWidth = Math.floor(w/columnNum);

					//Isotop Version 1
					var $containerV1 = jQuery('.isotope-items');
					$containerV1.find('.item').each(function() {
						var $item = jQuery(this),
						multiplier_w = $item.attr('class').match(/item-w(\d)/),
						multiplier_h = $item.attr('class').match(/item-h(\d)/),
						width = multiplier_w ? columnWidth*multiplier_w[1]-10 : columnWidth,
						height = multiplier_h ? columnWidth*multiplier_h[1]*0.7-10 : columnWidth*0.7-10;
						$item.css({ width: width, height: height });
					});


					return columnWidth;
				},
				isotope = function () {
					$container.isotope({
						resizable: true,
						itemSelector: '.item',
						masonry: {
							columnWidth: colWidth(),
							gutterWidth: 10
						}
					});
				};
				isotope();


				// bind filter button click
				jQuery('.isotope-filters').on( 'click', 'button', function() {
					var filterValue = jQuery( this ).attr('data-filter');
					$container.isotope({ filter: filterValue });
				});

				// change active class on buttons
				jQuery('.isotope-filters').each( function( i, buttonGroup ) {
					var $buttonGroup = jQuery( buttonGroup );
					$buttonGroup.on( 'click', 'button', function() {
						$buttonGroup.find('.active').removeClass('active');
						jQuery( this ).addClass('active');
					});
				});


				// Masonry Isotope
				var $masonryIsotope = jQuery('.isotope-masonry-items').isotope({
					itemSelector: '.item',
				});

				// bind filter button click
				jQuery('.isotope-filters').on( 'click', 'button', function() {
					var filterValue = jQuery( this ).attr('data-filter');
					$masonryIsotope.isotope({ filter: filterValue });
				});

				// change active class on buttons
				jQuery('.isotope-filters').each( function( i, buttonGroup ) {
					var $buttonGroup = jQuery( buttonGroup );
					$buttonGroup.on( 'click', 'button', function() {
						$buttonGroup.find('.active').removeClass('active');
						jQuery( this ).addClass('active');
					});
				});
			});
		});
	</script>
	<?php
	}    

    /**  custom post type.
    --------------------------------------------------------------------------------------------------- */
    public function _custom_post() {

    	$labels = array(
    		'name' => __( "Portfolio", 'wpdp' ),
    		'singular_name' => __( "Portfolio", 'wpdp' ),
    		'add_new_item' => __( "Add New Portfolio Package", 'wpdp' ),
    		'edit_item' => __( "Edit Portfolio Package", 'wpdp' ),
    		'new_item' => __( "New Portfolio Package", 'wpdp' ),
    		'view_item' => __( "View Portfolio", 'wpdp' ),
    		'search_items' => __( "Search Portfolio", 'wpdp' ),
    		'not_found' => __( "No Portfolio Found", 'wpdp' ),
    		'not_found_in_trash' => __( "No Portfolio Found in Trash", 'wpdp' ),
    		'parent_item_colon' => __( "Parent Portfolio", 'wpdp' ),
    		'menu_name' => __( "Portfolio", 'wpdp' )
    		);

    	$args = array(
    		'labels' => $labels,
    		'heirarchical' => false,
    		'descriptin' => 'View Portfolio',
    		'supports'  => array('title'),
    		'public' => true,
    		'show_ui' => true,
    		'menu_icon'           => 'dashicons-images-alt',
    		'show_in_menu' => true,
    		'show_in_nav_menu' => true,
    		'publicly_queryable' => true,
    		'exclude_from_search' => false,
    		'has_archive' => true,
    		'query_var' => true,
    		'can_export' => true,
    		'rewrite' => true,
    		'supports'	=> array('title', 'editor', 'thumbnail'),
    		'capability_type' => 'post'
    		);

    	register_post_type( 'portfolio', $args );



		/**  category.
		--------------------------------------------------------------------------------------------------- */
		$labels = array(
			'name'					=> __( 'Categories', 'Taxonomy plural name', 'wpdp' ),
			'singular_name'			=> __( 'Category', 'Taxonomy singular name', 'wpdp' ),
			'search_items'			=> __( 'Search Categories', 'wpdp' ),
			'popular_items'			=> __( 'Popular Categories', 'wpdp' ),
			'all_items'				=> __( 'All Categories', 'wpdp' ),
			'parent_item'			=> __( 'Parent Category', 'wpdp' ),
			'parent_item_colon'		=> __( 'Parent Category', 'wpdp' ),
			'edit_item'				=> __( 'Edit Category', 'wpdp' ),
			'update_item'			=> __( 'Update Category', 'wpdp' ),
			'add_new_item'			=> __( 'Add New Image Category', 'wpdp' ),
			'new_item_name'			=> __( 'New Category Name', 'wpdp' ),
			'add_or_remove_items'	=> __( 'Add or remove Categories', 'wpdp' ),
			'choose_from_most_used'	=> __( 'Choose from most used wpdp', 'wpdp' ),
			'menu_name'				=> __( 'Categories', 'wpdp' ),
			);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'query_var'         => true,
			'capabilities'      => array(),
			);

		register_taxonomy( 'portfolio-category', array( 'portfolio' ), $args );

	}

    /**  Show content in front end.
    --------------------------------------------------------------------------------------------------- */
    
    public function xtl_shortcode_include() {

    	require_once(XTL_P_DIR . '/inc/wp-portfolio-shortcode.php');

    }



    /**
     * Plugin url.
     *
     * @return string
     */
    public function plugin_url() {
    	if ( $this->plugin_url ) return $this->plugin_url;
    	return $this->plugin_url = untrailingslashit( plugins_url( '', __FILE__ ) );
    }

    /**
     * Plugin path.
     *
     * @return string
     */
    public function plugin_path() {
    	if ( $this->plugin_path ) return $this->plugin_path;
    	return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

}

} // end class
/**
*  make an object
*/

$GLOBALS['xtl_wp_portfolio'] = new Xtl_Wp_Portfolio;