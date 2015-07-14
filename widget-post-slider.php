<?php
/*
Plugin Name: Widget Post Slider
Plugin URI: http://shapedtheme.com/demo/plugins/widget-post-slider
Author: ShapedTheme
Author URI: http://shapedtheme.com
Description: Widget Post Slider to display posts image in a slider from category.
Version: 1.0
License: GPL2
Text Domain: shaped_theme
*/


// Don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

define('widget_post_slider_plugin_url', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );


function st_widget_post_slider_init_scripts()
    {
    	//CSS
    	wp_enqueue_style('wps_style', widget_post_slider_plugin_url. 'css/style.css' );
    	wp_enqueue_style('owl_carousel_css', widget_post_slider_plugin_url. 'css/bootstrap.min.css');
        
        //JS
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script('owl_carousel_js', plugins_url('/js/bootstrap.min.js', __FILE__), array ('jquery'));
        wp_enqueue_script('scripts_js', plugins_url('/js/scripts.js', __FILE__), array('jquery'));

    }
add_action("init","st_widget_post_slider_init_scripts");


//Thumbnail Size
add_image_size('wps_thumbnail_size', 300, 230, TRUE);


//wadget
add_action('widgets_init','register_shapedtheme_widget_post_slider');

function register_shapedtheme_widget_post_slider()
{
	register_widget('ST_Widget_Post_Slider');
}

class ST_Widget_Post_Slider extends WP_Widget{

	function ST_Widget_Post_Slider()
	{
		$this->WP_Widget( 'ST_Widget_Post_Slider', __('Widget Post Slider', 'shaped_theme') , array('description' => __('Widget Post Slider to display posts', 'shaped_theme') ) );
	}


	/*-------------------------------------------------------
	 *				Front-end display of widget
	 *-------------------------------------------------------*/

	function widget($args, $instance)
	{
		extract($args);

		$title 			= apply_filters('widget_title', $instance['title'] );
		$count 			= $instance['count'];
		$cat_ID 		= $instance['cat_name'];
		
		echo $before_widget;

		$output = '';

		if ( $title )
			echo $before_title . $title . $after_title;

		global $post;


		$args = array( 
			'posts_per_page' 	=> $count,
			'category'			=> $cat_ID
		);

		$posts = get_posts( $args );

		if(count($posts)>0){
			$output .='<div id="gallery-carousel" class="carousel slide" data-ride="carousel">
                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">';

			foreach ($posts as $post): setup_postdata($post);
				$output .='<div class="item">';

					if(has_post_thumbnail()):
						$output .='<div class="post-slider">';
						$output .='<a href="'.get_permalink().'">'.get_the_post_thumbnail($post->ID, 'wps_thumbnail_size', array('class' => 'img-responsive')).'</a>';
						$output .= '<div class="carousel-caption"><p><a href="'.get_permalink().'">'. get_the_title() .'.</a></p></div>';
						$output .='</div>';
					endif;

					


				$output .='</div>';
			endforeach;

			wp_reset_query();

			$output .=' </div>

                <!-- Controls -->
                <a class="left carousel-control" href="#gallery-carousel" role="button" data-slide="prev">
                  <i class="glyphicon glyphicon-menu-left"></i>
                </a>
                <a class="right carousel-control" href="#gallery-carousel" role="button" data-slide="next">
                  <i class="glyphicon glyphicon-menu-right"></i>
                </a>
              </div>';
		}


		echo $output;

		echo $after_widget;
	}


	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;

		$instance['title'] 			= strip_tags( $new_instance['title'] );
		$instance['cat_name'] 		= strip_tags( $new_instance['cat_name'] );
		$instance['count'] 			= strip_tags( $new_instance['count'] );

		return $instance;
	}


	function form($instance)
	{
		$defaults = array( 
			'title' 	=> 'Widget Post Slider',
			'cat_name' 	=> ' ',
			'width' 	=> '300',
			'count' 	=> 5
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Widget Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'cat_name' ); ?>">Select Category</label>
			<?php 
				$categories = get_categories(array('hierarchical' => false));
				if(isset($instance['cat_name'])) $cat_ID = $instance['cat_name'];
			?>
			<select class="widefat" id="<?php echo $this->get_field_id( 'cat_name' ); ?>" name="<?php echo $this->get_field_name( 'cat_name' ); ?>">


			<option value='all' <?php if ('all' == $instance['cat_name']) echo 'selected="selected"'; ?>><?php _e('All categories', 'shaped_theme') ?></option>
			<?php $categories = get_categories('hide_empty=0&depth=1&type=post'); ?>
			<?php foreach($categories as $category) { ?>
			<option value='<?php echo $category->term_id; ?>' <?php if ($category->term_id == $instance['cat_name']) echo 'selected="selected"'; ?>><?php echo $category->cat_name; ?></option>
			<?php } ?>



			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e('Slide Count', 'shaped_theme') ?></label>
			<input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $instance['count']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}