<?php
/*
 * Plugin Name: WordPress Plugin Template
 * Version: 1.0
 * Plugin URI: http://www.hughlashbrooke.com/
 * Description: This is your starter template for your next WordPress plugin.
 * Author: Hugh Lashbrooke
 * Author URI: http://www.hughlashbrooke.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: wordpress-plugin-template
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Hugh Lashbrooke
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if (! defined( 'CMB_DEV')) {
	require_once( 'cmb/custom-meta-boxes.php' );
}

// Load plugin class files
require_once( 'includes/class-wordpress-plugin-template.php' );
require_once( 'includes/class-wordpress-plugin-template-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-wordpress-plugin-template-admin-api.php' );
require_once( 'includes/lib/class-wordpress-plugin-template-post-type.php' );
require_once( 'includes/lib/class-wordpress-plugin-template-taxonomy.php' );


class BS_Custom_Widget extends WP_Widget {
	public $fields;
	public $title;
	function __construct($name , $description, $title , $fields) {
		$this->fields = $fields;
		$this->title = $title;
		parent::__construct(false, $name, array( 'description' => $description )); 
	}
		/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
		public function widget( $args, $instance ) {
			echo $args['before_widget'];
			$instance['title'] = $this->title;
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			
			foreach ($this->fields as $field) {
				echo $instance[$field];
			}

			echo $args['after_widget'];
		}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( "Title:" ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $this->title ); ?>">
		</p>
		<?php
		foreach ($this->fields as $field) {
			?>
			<p>
				<label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e( "$field:" ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" type="text" value="<?php echo $instance[$field]; ?>" >
			</p>
			<?php
		}
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		foreach ($this->fields as $field) {
			$instance[$field] = ( ! empty( $new_instance[$field] ) ) ? strip_tags( $new_instance[$field] ) : '';
		}
		return $instance;
	}
}

class WP_Widget_Custom_Factory extends WP_Widget_Factory {
	function register($widget_class , $widget_args, $fields) {
		extract($widget_args);
		$this->widgets[$widget_class] = new BS_Custom_Widget($name , $description , $title , $fields);
	}
}//end of class



function WordPress_Plugin_Template () {
	$instance = WordPress_Plugin_Template::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = WordPress_Plugin_Template_Settings::instance( $instance );
	}

	return $instance;
}

WordPress_Plugin_Template();


//code to initiate custom widget
add_action( 'widgets_init', function(){
	$WP_Custom_Widget = new WP_Widget_Custom_Factory();
	$WP_Custom_Widget->register(
		'BS_Test_Widget' , 
		array(
			'name'=>'bob', 'description'=>'This is a description',
			'title' => 'This is still the title'
			) , 
		array(
			'This is a test' ,
			'input_one') );
});


// SHORTCODES
// [bartag foo="foo-value"]
// function bartag_func( $atts ) {
//     $a = shortcode_atts( array(
//         'foo' => 'something',
//         'bar' => 'something else',
//     ), $atts );

//     return "foo = {$a['foo']}";
// }
// add_shortcode( 'bartag', 'bartag_func' );
// 

// WIDGET
// 
