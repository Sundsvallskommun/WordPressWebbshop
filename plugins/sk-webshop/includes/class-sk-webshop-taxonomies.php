<?php
/**
 * SK_Webshop_Taxonomies
 * =====================
 *
 * Registers and handles all taxonomy related things.
 *
 * Register hooks and filters.
 *
 * @since   0.1
 * @package SK_Webshop
 */

class SK_Webshop_Taxonomies {

	/**
	 * The class instance.
	 * @var SK_Webshop|null
	 */
	private static $instance = null;

	/**
	 * Taxonomy name for the unit type.
	 * @var string
	 */
	private static $UNIT_TYPE_TAX = 'product_unit_type';

	/**
	 * String representation of the metakey for unit type.
	 * @var string
	 */
	private static $UNIT_TYPE_META = '_unit_type';

	/**
	 * Inits the class.
	 */
	public function __construct() {
		// Register unit type taxonomy.
		add_action( 'init', array( $this, 'add_unit_type_taxonomy' ) );

		// Display unit type options on product admin screen.
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_unit_type_field' ) );

		// Save unit type option on product admin screen.
		add_action( 'save_post', array( $this, 'save_unit_type_field' ) );
	}

	/**
	 * Function that returns a singleton instance
	 * of the class.
	 * @return SK_Webshop
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Registers a custom taxonomy for the price per
	 * product.
	 * @return void
	 */
	public function add_unit_type_taxonomy() {
		$labels = array(
			'name'							=> __( 'Enhetstyper', 'sk-webshop' ),
			'singular_name'					=> __( 'Enhetstyp', 'sk-webshop' ),
			'menu_name'						=> __( 'Enhetstyper', 'sk-webshop' ),
			'all_items'						=> __( 'Alla enhetstyper', 'sk-webshop' ),
			'parent_item'					=> __( 'Förälderenhetstyp', 'sk-webshop' ),
			'parent_item_colon'				=> __( 'Förälderenhetstyp:', 'sk-webshop' ),
			'new_item_name'					=> __( 'Ny enhetstyp', 'sk-webshop' ),
			'add_new_item'					=> __( 'Lägg till ny enhetstyp', 'sk-webshop' ),
			'edit_item'						=> __( 'Redigera enhetstyp', 'sk-webshop' ),
			'update_item'					=> __( 'Uppdatera enhetstyp', 'sk-webshop' ),
			'separate_items_with_commas'	=> __( 'Separera enhetstyper med kommatecken', 'sk-webshop' ),
			'search_items'					=> __( 'Sök enhetstyper', 'sk-webshop' ),
			'add_or_remove_items'			=> __( 'Lägg till eller ta bort Enhetstyper', 'sk-webshop' ),
			'choose_from_most_used'			=> __( 'Välja from de mest använda Enhetstyperna', 'sk-webshop' ),
		);
		$args = array(
			'labels'						=> $labels,
			'hierarchical'					=> true,
			'public'						=> false,
			'show_ui'						=> true,
			'show_admin_column'				=> true,
			'show_in_nav_menus'				=> true,
			'show_tagcloud'					=> true,
		);
		register_taxonomy( self::$UNIT_TYPE_TAX, 'product', $args );
		register_taxonomy_for_object_type( self::$UNIT_TYPE_TAX, 'product' );
	}

	/**
	 * Adds a select element with all unit types as options.
	 * @return void
	 */
	public function add_unit_type_field() {
		$unit_types = get_terms( array(
			'taxonomy'		=> 'product_unit_type',
			'hide_empty'	=> false,
		) );

		// Check if this product already has one selected.
		global $post;
		if ( $selected_unit_type = get_the_terms( $post, self::$UNIT_TYPE_TAX ) ) {
			$selected_unit_type = reset( $selected_unit_type );
		}

		echo '<div class="options_group">';

			echo '<p class="form-field ' . self::$UNIT_TYPE_META .'_field"><label for="' . self::$UNIT_TYPE_META . '">' . __( 'Enhetstyp', 'sk-webshop' ) . '</label><select id="' . self::$UNIT_TYPE_META . '" name="' . self::$UNIT_TYPE_META . '" class="short select">';

				echo '<option value="0">' . __( '---Ingen vald---', 'sk-webshop' ) . '</option>';

				foreach ( $unit_types as $term ) {
					echo '<option value="' . $term->term_id . '" ' . selected( $term->term_id, $selected_unit_type->term_id, false ) . '>' . $term->name . '</option>';
				}

			echo '</select></p>';

		echo '</div>';
	}

	/**
	 * Save the unit type on post_save.
	 * @param  integer $post_id
	 * @return void
	 */
	public function save_unit_type_field( $post_id ) {
		// Don't save unless it's a product.
		if ( get_post_type( $post_id ) !== 'product' ) {
			return false;
		}

		// Don't save revisions or autosaves.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}

		// Saving is different depending for bulk/quick edit so
		// we need to check for that and do it abit differently.
		if ( $_REQUEST[ 'bulk_edit' ] || $_REQUEST[ 'woocommerce_quick_edit' ] ) {
			// Check if a unit type was provided.
			if ( ! empty( $_REQUEST[ 'tax_input' ][ self::$UNIT_TYPE_META ] ) ) {
				wp_set_object_terms( $post_id, (int) $_REQUEST[ 'tax_input' ][ self::$UNIT_TYPE_META ], self::$UNIT_TYPE_TAX );
			}
		} else {
			// Check if a unit type was provided.
			if ( ! empty( $_REQUEST[ self::$UNIT_TYPE_META ] ) ) {
				wp_set_object_terms( $post_id, (int) $_REQUEST[ self::$UNIT_TYPE_META ], self::$UNIT_TYPE_TAX );
			} else {
				wp_delete_object_term_relationships( $post_id, self::$UNIT_TYPE_TAX );
			}
		}
	}

}