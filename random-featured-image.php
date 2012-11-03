<?php
/*
Plugin Name: Random Featured Image
Plugin URI: http://www.purjelautaliitto.fi/
Description: A widget that lists random featured images from a chosen category.
Version: 1.1
Author: Mikko Vatanen
Author URI: http://www.purjelautaliitto.fi/
Text Domain: random-featured-image
License: GPL2
*/

class RandomFeaturedImage extends WP_Widget {

	function RandomFeaturedImage() {
			$widget_ops = array('classname' => 'random_featured_image', 'description' => __( 'random featured images from a chosen category', 'random-featured-image') );
			$this->WP_Widget('RandomFeaturedImage', __('Random Featured Images', 'random-featured-image'), $widget_ops);
	}

    function div_begin( $class ) {
        echo "\n<div class=\"$class\">\n";
    }

    function div_end() {
        echo "\n</div>\n";
    }

	function widget( $args, $instance ) {
			extract( $args );

			//$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( ' Random Featured Images' , 'random-featured-image') : $instance['title']);

			echo $before_widget;

			if ( $title ) {
				if ($instance['postlink'] == 1)  {
					$before_title .= '<a href="' . esc_url(get_category_link($instance['cat'])) . '">';
					$after_title = '</a>';
				}
				echo $before_title . $title . $after_title;
			}

            $this->div_begin('random_featured_image_cycle');

            $random = new WP_Query("cat=".$instance['cat']."&showposts=".$instance['showposts']."&orderby=rand");

            // the Loop
            if ($random->have_posts()) :
                $hiddenclass="";
                while ($random->have_posts()) : $random->the_post();

                    $this->div_begin("random_featured_image_container $hiddenclass");
                    $hiddenclass = "hidden";

                    if ($instance['content'] != 'excerpt-notitle' && $instance['content'] != 'content-notitle') {
                        $this->div_begin('random_featured_image_title');
                        echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                        $this->div_end();
                    }

                    if ($instance['content'] == 'excerpt' || $instance['content'] == 'excerpt-notitle') {
                        if (function_exists('the_excerpt_reloaded'))
                            the_excerpt_reloaded($instance['words'], $instance['tags'], 'content', FALSE, '', '', '1', '');
                        else the_excerpt();  // this covers Advanced Excerpt as well as the built-in one
                    }

                    if ($instance['content'] == 'content' || $instance['content'] == 'content-notitle') the_content();

                    if ($instance['content'] == 'title-image') {

                    if ( current_theme_supports('get-the-image') ) {
                        get_the_image( array(
                            'meta_key' => 'Thumbnail',
                            'size' => 'thumbnail',
                            'image_class' => 'random_featured_image_image',
                            'width' => 150,
                            'height' => 150,
                            'default_image' => get_template_directory_uri() . '/images/archive-thumbnail-placeholder.gif' ) );
                        }
                    }

                    $first = false;

                    $this->div_end();

                endwhile;
            endif;

            $this->div_end();

			echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['cat'] = $new_instance['cat'];
			$instance['showposts'] = $new_instance['showposts'];
			$instance['content'] = $new_instance['content'];
			$instance['postlink'] = $new_instance['postlink'];
			$instance['words'] = $new_instance['words'];
			$instance['tags'] = $new_instance['tags'];
			return $instance;
	}

	function form( $instance ) {
			//Defaults
				$instance = wp_parse_args( (array) $instance, array(
						'title' => __(' Recent Posts', 'random-featured-image'),
						'cat' => 1,
						'showposts' => 1,
						'content' => 'title',
						'postlink' => 0,
						'words' => '99999',
						'tags' => '<p><div><span><br><img><a><ul><ol><li><blockquote><cite><em><i><strong><b><h2><h3><h4><h5><h6>'));
	?>

<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'random-featured-image'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
	name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
</p>

<p><label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e('Show posts from category:', 'random-featured-image'); ?></label>
<?php wp_dropdown_categories(array('name' => $this->get_field_name('cat'), 'hide_empty'=>0, 'hierarchical'=>1, 'selected'=>$instance['cat'])); ?></label>
</p>

<p>
<input id="<?php echo $this->get_field_id('postlink'); ?>" name="<?php echo $this->get_field_name('postlink'); ?>"
	type="checkbox" <?php if ($instance['postlink']) { ?> checked="checked" <?php } ?> value="1" />
<label for="<?php echo $this->get_field_id('postlink'); ?>"><?php _e('Link widget title to category archive', 'random-featured-image'); ?></label>
</p>

<p><label for="<?php echo $this->get_field_id('showposts'); ?>"><?php _e('Number of posts to show:', 'random-featured-image'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('showposts'); ?>" name="<?php echo $this->get_field_name('showposts'); ?>"
	type="text" value="<?php echo $instance['showposts']; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('content'); ?>"><?php _e('Display:', 'random-featured-image'); ?></label>
<select id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" class="postform">
	<option value="title"<?php selected( $instance['content'], 'title' ); ?>><?php _e('Title Only', 'random-featured-image'); ?></option>
	<option value="title-image"<?php selected( $instance['content'], 'title-image' ); ?>><?php _e('Title and Image', 'random-featured-image'); ?></option>
	<option value="excerpt"<?php selected( $instance['content'], 'excerpt' ); ?>><?php _e('Title and Excerpt', 'random-featured-image'); ?></option>
	<option value="excerpt-notitle"<?php selected( $instance['content'], 'excerpt-notitle' ); ?>><?php _e('Excerpt without Title', 'random-featured-image'); ?></option>
	<option value="content"<?php selected( $instance['content'], 'content' ); ?>><?php _e('Title and Content', 'random-featured-image'); ?></option>
	<option value="content-notitle"<?php selected( $instance['content'], 'content-notitle' ); ?>><?php _e('Content without Title', 'random-featured-image'); ?></option>
</select>
</p>

<?php
if (function_exists('the_excerpt_reloaded')) { ?>
<p>
<label for="<?php echo $this->get_field_id('words'); ?>"><?php _e('Limit excerpts to how many words?:', 'random-featured-image'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('words'); ?>" name="<?php echo $this->get_field_name('words'); ?>"
	type="text" value="<?php echo $instance['words']; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Allowed HTML tags in excerpts:', 'random-featured-image'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>"
	type="text" value="<?php echo htmlspecialchars($instance['tags'], ENT_QUOTES); ?>" />
<br />
<small><?php _e('E.g.: ', 'random-featured-image'); ?>&lt;p&gt;&lt;div&gt;&lt;span&gt;&lt;br&gt;&lt;img&gt;&lt;a&gt;&lt;ul&gt;&lt;ol&gt;&lt;li&gt;&lt;blockquote&gt;&lt;cite&gt;&lt;em&gt;&lt;i&gt;&lt;strong&gt;&lt;b&gt;&lt;h2&gt;&lt;h3&gt;&lt;h4&gt;&lt;h5&gt;&lt;h6&gt;
</small>
</p>
<?php } // end if function_exists

	} // function form
} // widget class

function random_feat_image_from_cat_init() {
	register_widget('RandomFeaturedImage');
}

function random_featured_image_enqueue_scripts() {
    wp_deregister_script( 'random_featured_image_script' );
    wp_register_script( 'random_featured_image_script', plugins_url('js/main.js', __FILE__));
    wp_enqueue_script( 'random_featured_image_script' );

    wp_register_style( 'random_featured_image_style', plugins_url('css/style.css', __FILE__));
    wp_enqueue_style( 'random_featured_image_style' );
}

add_action('widgets_init', 'random_feat_image_from_cat_init');
add_action('wp_enqueue_scripts', 'random_featured_image_enqueue_scripts');



// i18n
$plugin_dir = basename(dirname(__FILE__)). '/languages';
load_plugin_textdomain( 'RandomFeaturedImage', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );
?>
