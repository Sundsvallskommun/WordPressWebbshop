<?php
/**
 * SK_Webshop_Category_Menu
 * ========================
 *
 * Handles the custom product category menu.
 *
 * @since   20200130
 * @package SK_Webshop
 */

use \Timber\Term;

class SK_Webshop_Category_Menu {

	/**
	 * @var boolean
	 */
	private $should_output = false;

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_filter( 'nav_menu_css_class', [ $this, 'set_init_vars' ], 10, 4 );
		add_filter( 'wp_nav_menu', [ $this, 'maybe_output_category_menu' ], 10, 2 );
	}

	/**
	 * Sets the initial variables, such as $should_output
	 * and css classes in a nav menu item if this is
	 * the correct item.
	 * @param  array    $classes
	 * @param  WP_Post  $item
	 * @param  stdClass $args
	 * @param  integer  $depths
	 * @return array
	 */
	public function set_init_vars( $classes, $item, $args, $depths ) {
		if ( 'custom' === $item->type && 'Sortiment' === $item->title ) {
			$this->should_output = true;
			$classes[] = 'js-category-menu';
		}
		return $classes;
	}


	/**
	 * Outputs the category menu if applicable.
	 * @param  string $output
	 * @param  array  $args
	 * @return string
	 */
	public function maybe_output_category_menu( $output, $args ) {
		if ( $this->should_output ) {
			$output .= $this->output_category_menu( false );
		}

		return $output;
	}

	/**
	 * Outputs the category menu markup.
	 * @param  boolean $echo
	 * @return string
	 */
	public function output_category_menu( $echo = true ) {
		$cats = $this->get_category_tree();

		$template = __DIR__ . '/views/category-menu.twig';
		$args     = [
			'categories' => $cats,
		];
		if ( $echo ) {
			\Timber::render( $template, $args );
		} else {
			return Timber::compile( $template, $args );
		}
	}

	/**
	 * Returns the product category tree.
	 * @return array
	 */
	public function get_category_tree() {
		$terms = get_terms( [
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'fields'     => 'ids',
			'parent'     => 0,
		] );

		return array_map( function( $term ) {
			return new Term( $term );
		}, $terms );
	}

}
new SK_Webshop_Category_Menu;
