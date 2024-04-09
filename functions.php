<?php
/*
Plugin Name: Testimonials Post Types
Plugin URI: 
Description: Add post types for testimonials
Version: 0.1
Author: Anirban Sil
Author URI: 
License: 
Text Domain: testimonial
*/

if (! defined('ABSPATH')) {
    exit;
}

add_action( 'init', 'ht_custom_post_testimonials' );

function ht_custom_post_testimonials() {
	
    $labels = array(
        'name' => __( 'Testimonials', 'Post Type General Name', 'testimonials' ),
        'singular_name' => __( 'Testimonial', 'Post Type General Name', 'testimonials' ),
        'menu_name' => __( 'Testimonials', 'testimonials' ),
        'name_admin_bar' => __( 'Testimonials', 'testimonials' ),
        'archives' => __( 'Testimonial Archives', 'testimonials' ),
        'attributes' => __( 'Testimonial Attributes', 'testimonials' ),
        'parent_item_colon' => __( 'Parent Testimonial', 'testimonials' ),
        'all_items' => __( 'All Testimonials', 'testimonials' ),
        'add_new' => __( 'New Testimonial', 'testimonials' ),
        'add_new_item' => __( 'Add New Testimonial', 'testimonials' ),
        'edit_item' => __( 'Edit Testimonial', 'testimonials' ),
        'update_item' => __( 'Update Testimonial', 'testimonials' ),
        'new_item' => __( 'New Testimonial', 'testimonials' ),
        'view_item' => __( 'View Testimonials', 'testimonials' ),
        'search_items' => __( 'Search Testimonials', 'testimonials' ),
        'not_found' =>  __( 'No Testimonials Found', 'testimonials' ),
        'not_found_in_trash' => __( 'No Testimonials found in Trash'),
        'featured_image' => __( 'Featured Image', 'testimonials' ),
        'set_featured_image' => __( 'Set Featured Image', 'testimonials' ),
        'remove_featured_image' => __( 'Remove Featured Image', 'testimonials' ),
        'use_featured_image' => __( 'Use as Featured Image', 'testimonials' ),
        'insert_into_item' => __( 'Insert Into Testimonial', 'testimonials' ),
        'uploaded_to_this_item' => __( 'Uploaded To This Testimonial', 'testimonials' ),
        'items_list' => __( 'Testimonials list', 'testimonials' ),
        'items_list_navigation' => __( 'Testimonials list navigation', 'testimonials' ),
        'filter_items_list' => __( 'Filter Testimonials List', 'testimonials' ),
    );
	
    $args = array(
        'labels'                => $labels,
        'description'           => 'Holds our testimonials post specific data',
        'public'                => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-testimonial',
        'supports'              => array( 'title', 'editor', 'thumbnail' ), // 'excerpt', 'comments', 'custom-fields'
        'has_archive'           => true,
        'hierarchical'          => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'can_export'            => true,
        'exclude_from_search'   => false,
        'show_in_rest'          => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'rewrite'               => array( 'slug' => 'testimonials' ),
    );
	
    register_post_type('testimonials', $args);
}



class Testimonial_Related_Data {
	private $config = '{"title":"Testimonial Related Data","prefix":"testimonial_related_data_","domain":"testimonial-related-data","class_name":"Testimonial_Related_Data","post-type":["testimonials"],"context":"normal","priority":"default","fields":[
        {"type":"text","label":"Company Name","id":"testimonial_related_data_company-name"},
        {"type":"number","label":"Rating Points","id":"testimonial_related_data_rating-points"}]}';

	public function __construct() {
		$this->config = json_decode( $this->config, true );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_post' ] );
	}

	public function add_meta_boxes() {
		foreach ( $this->config['post-type'] as $screen ) {
			add_meta_box(
				sanitize_title( $this->config['title'] ),
				$this->config['title'],
				[ $this, 'add_meta_box_callback' ],
				$screen,
				$this->config['context'],
				$this->config['priority']
			);
		}
	}

	public function save_post( $post_id ) {
		foreach ( $this->config['fields'] as $field ) {
			switch ( $field['type'] ) {
				case 'url':
					if ( isset( $_POST[ $field['id'] ] ) ) {
						$sanitized = esc_url_raw( $_POST[ $field['id'] ] );
						update_post_meta( $post_id, $field['id'], $sanitized );
					}
					break;
				default:
					if ( isset( $_POST[ $field['id'] ] ) ) {
						$sanitized = sanitize_text_field( $_POST[ $field['id'] ] );
						update_post_meta( $post_id, $field['id'], $sanitized );
					}
			}
		}
	}

	public function add_meta_box_callback() {
		$this->fields_table();
	}

	private function fields_table() {
		?><table class="form-table" role="presentation">
			<tbody><?php
				foreach ( $this->config['fields'] as $field ) {
					?><tr>
						<th scope="row"><?php $this->label( $field ); ?></th>
						<td><?php $this->field( $field ); ?></td>
					</tr><?php
				}
			?></tbody>
		</table><?php
	}

	private function label( $field ) {
		switch ( $field['type'] ) {
			default:
				printf(
					'<label class="" for="%s">%s</label>',
					$field['id'], $field['label']
				);
		}
	}

	private function field( $field ) {
		switch ( $field['type'] ) {
			case 'number':
				$this->input_minmax( $field );
				break;
			default:
				$this->input( $field );
		}
	}

	private function input( $field ) {
		printf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s">',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'], $field['id'],
			isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
			$field['type'],
			$this->value( $field )
		);
	}

	private function input_minmax( $field ) {
		printf(
			'<input class="regular-text" id="%s" %s %s name="%s" %s type="%s" value="%s" step="0.50" min="1" max="5">',
			$field['id'],
			isset( $field['max'] ) ? "max='{$field['max']}'" : '',
			isset( $field['min'] ) ? "min='{$field['min']}'" : '',
			$field['id'],
			isset( $field['step'] ) ? "step='{$field['step']}'" : '',
			$field['type'],
			$this->value( $field )
		);
	}

	private function value( $field ) {
		global $post;
		if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
			$value = get_post_meta( $post->ID, $field['id'], true );
		} else if ( isset( $field['default'] ) ) {
			$value = $field['default'];
		} else {
			return '';
		}
		return str_replace( '\u0027', "'", $value );
	}

}
new Testimonial_Related_Data;


// Shortcode

function testimonials_section_shortcode(){

    $php_html_file_path = plugin_dir_path(__FILE__) . '../view/testimonials.php';
    ob_start();
    include $php_html_file_path;
    return ob_get_clean();
}
add_shortcode('testimonials-section', 'testimonials_section_shortcode');


add_action('admin_footer','plugin_note');
function plugin_note(){
    global $typenow,$pagenow;
    if  (in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ))  && "testimonials" == $typenow ) {
        ?>
        <SCRIPT TYPE="text/javascript">
            jQuery(document).ready(function(){
                var myDiv = jQuery('<div>');
                myDiv.css("padding","5px 0px 15px 0px");
                myDiv.html("Use the shortcode [testimonials-section]");
                jQuery(".wrap").find("h1").after(myDiv);
            });
        </SCRIPT>
    <?php
    }
}
?>