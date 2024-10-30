<?php
/*
Plugin Name: Kino Gallery Plugin
Version: 1.0
Plugin URI: http://www.kinocreative.co.uk
Description: Simple gallery plugin allowing pics to be navigated from sidebar widget.
Author: Richard Telford
Author URI: http://www.kinocreative.co.uk
*/

include "config.php";

if ( function_exists( 'add_theme_support' ) )
{ // Added in 2.9
	add_theme_support( 'post-thumbnails' );
}

function kino_gallery_register_post_type_pictures()
{
	register_post_type('pictures', array(
		'labels' => array(
			'name' => _x('Kino Gallery', 'post type general name'),
			'singular_name' => _x('Pictures', 'post type singular name'),
			'add_new' => _x('Add New', 'pictures'),
			'add_new_item' => __('Add New Picture'),
			'edit_item' => __('Edit Picture'),
			'new_item' => __('New Picture'),
			'view_item' => __('View Picture'),
			'search_items' => __('Search Pictures'),
			'not_found' =>  __('No picture/s found'),
			'not_found_in_trash' => __('No picture/s found in Trash'), 
			'parent_item_colon' => ''),
		'public' => true,
		'show_ui' => true, // UI in admin panel
		'_builtin' => false, // It's a custom post type, not built in!
		'capability_type' => 'post',
		'hierarchical' => true,
		'rewrite' => array('slug' => 'gallery-pics'), // Permalinks format or set to true
		//'menu_icon' => '/wp-content/themes/pelicanpr/images/something.png',
		'supports' => array("title","editor","thumbnail","page-attributes")
	));	
}

/*
function kino_gallery_post_type_pictures_create_taxonomies() 
{
	$labels = array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ), 
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' ),
	); 	
	
	register_taxonomy('picture_category', 'pictures', array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'gallery-category') 
	));
}
*/


function kino_gallery_post_type_custom_fields_pictures()
{
	global $post;
	
	$custom 			= get_post_custom($post->ID);
	$_picture_url 	= $custom["_picture_url"][0];
	?>
	<div class="row">
		<span class="label"><label><strong>Pic URL:</strong></label> </span>
		<span class="field">
			<input type="text" name="_picture_url" value="<?php print $_picture_url; ?>" class="textbox"/>
			<?php
			if($_picture_url)
			{
				?>
				<a href="<?php print $_picture_url; ?>" target="_blank"><img src="<?php print $_picture_url; ?>" height="150"/></a>
				<?php
			}
			?>
		</span>
		
		
		<br class="clear" />
	</div>
	<?php
}



function kino_gallery_save_post_type_pictures($post_id)
{
	global $post;
	
	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
	// to do anything
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
	
	
	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return $post_id;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;
	}
	
	update_post_meta($post->ID, "_picture_url", $_POST["_picture_url"]);
}

function kino_gallery_meta_init()
{
	add_meta_box("pictures-meta", "Post Options", "kino_gallery_post_type_custom_fields_pictures", "pictures", "advanced", "high");
	
}


	
	
// WIDGET
class KinoGallery extends WP_Widget
{
    /** constructor */
    function KinoGallery()
	{
		$name = "Kino Gallery";
		$description = "Show your pics in sidebar";
        parent::WP_Widget(false, $name, array("description"=>$description));	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance)
	{		
		global $wpdb, $post;
		
		$_wpdb = $wpdb;
		
		
		
        extract( $args );
		
       	$title 	= $instance['title'];
		
		$thumb_count = get_option(KINO_PLUGIN_SHORT_NAME."_thumbnail_count") ? get_option(KINO_PLUGIN_SHORT_NAME."_thumbnail_count") : 4;
				
		$thumb_size = get_option(KINO_PLUGIN_SHORT_NAME."_thumbnail_size") ? get_option(KINO_PLUGIN_SHORT_NAME."_thumbnail_size") : "";
			
		$css = get_option(KINO_PLUGIN_SHORT_NAME."_thumbnail_css");
		
		// NEED TO SEE IF WE ARE IN CATEGORY?
		$term = array();
		
		// IF IT'S A SINGLE PICTURE .. GET IT'S CATEGORY
		/*if(is_single() && $post->post_type == "pictures")
		{
			$terms = wp_get_post_terms($post->ID, "picture_category");
			$term = $terms;
			
		}*/
		
		if(1==2)
		//if( (is_archive() || is_single()) && $post->post_type == "pictures" && get_option(KINO_PLUGIN_SHORT_NAME."_widget_category_specific") )
		{
			$term = wp_get_post_terms($post->ID,"picture_category");		
			$title = str_replace("%category%", $term[0]->name, $title);
		}
		else
		{
			$title = str_replace("%category%", "", $title);	
		}
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
		
		// IF IN A CATEGORY
		if(1==2)
		//if($term && get_option(KINO_PLUGIN_SHORT_NAME."_widget_category_specific") )
		{
			// GET PICS BELONGING TO THIS CATEGORY
			$query = "SELECT * from $wpdb->terms AS t
			INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id 
			INNER JOIN $wpdb->term_relationships AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id 
			INNER JOIN $wpdb->posts AS p ON tr.object_id = p.ID 
			
			WHERE p.post_status = 'publish' 
			AND p.post_type = 'pictures' 
			AND t.term_id = '".$term[0]->term_id."' 
			ORDER BY p.post_date DESC
			";
			
			$pics = $_wpdb->get_results($query);
			
			if(is_single())
			{
				$post_id = $post->ID;
			}
			else
			{
				$post_id = $pics[0]->ID;
			}
			
		}
		else
		{	
			// GET PICS
			$pics = get_posts("post_type=pictures&numberposts=-1");
			
			$post_id = $post->ID;
		}
		
		
		if(count($pics))
		{
			$pic_counter = 1;
			?>
			<div class="kinogallery">
			<?php
			foreach($pics as $x)
			{
				
				// IF REMAINDER IS ZEROs
				$class = !(($pic_counter) % $thumb_count) ? "last" : "";
				
				// ID OF POST USED TO DETERMINE OPACITY LEVEL
				$selected = false;
				if($x->ID == $post_id)
				{
					$selected = true;	
				}
				$pic_meta = get_post_custom($x->ID);
				$featured_image = $pic_meta['_picture_url'][0];
				?>
				<div class="kinogallery-pic <?php print $class; ?>" <?php print ($css)?("style='$css'"):(""); ?>><a <?php print ($selected)?("class='selected'"):(""); ?> href="<?php print get_permalink($x->ID); ?>" title="<?php print $x->post_title; ?>"><img src="<?php print KINO_PLUGIN_PATH."/timthumb.php?src=".$featured_image."&w=".$thumb_size."&h=".$thumb_size."&zc=1"; ?>" width="<?php print $thumb_size; ?>" height="<?php print $thumb_size; ?>" alt="<?php print $x->post_title; ?>" /></a></div>
				<?php
				$pic_counter++;
			}
			?>
			<br class="clear" />
			<p><a href="http://www.kinocreative.co.uk/">Developed by Kino Creative</a></p>
			</div>
			<?php
		}
		
		echo $after_widget; 
		?>
		<br class="clear" /><br/>
		<?php
	}

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance)
	{				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance)
	{				
        $title 		= esc_attr($instance['title']);
       	/* $width 		= esc_attr($instance['width']);
        $height 	= esc_attr($instance['height']);
        $columns 	= esc_attr($instance['columns']);
        $css 		= esc_attr($instance['css']);*/
        ?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?><input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" class="long widefat" value="<?php print $title; ?>"/></label></p>
		<?php
		/*<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:'); ?><input type="text" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" class="long widefat" value="<?php print $width; ?>"/></label></p>
		<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:'); ?><input type="text" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" class="long widefat" value="<?php print $height; ?>"/></label></p>
		<p><label for="<?php echo $this->get_field_id('columns'); ?>"><?php _e('Pics per row:'); ?><input type="text" id="<?php echo $this->get_field_id('columns'); ?>" name="<?php echo $this->get_field_name('columns'); ?>" class="long widefat" value="<?php print $columns; ?>"/></label></p>
		<p><label for="<?php echo $this->get_field_id('css'); ?>"><?php _e('CSS style:'); ?><input type="text" id="<?php echo $this->get_field_id('css'); ?>" name="<?php echo $this->get_field_name('css'); ?>" class="long widefat" value="<?php print $css; ?>"/></label></p>
		*/
		?><?php
    }
}


function kino_gallery_wp_head()
{
	?><link rel="stylesheet" type="text/css" href="<?php print KINO_PLUGIN_PATH; ?>/css/main.css" /><?php
}

function kino_gallery_admin_head()
{
	?>
	<link type="text/css" rel="stylesheet" href="<?php print KINO_PLUGIN_PATH; ?>/css/admin.css"  />
	<?php	
}

function kino_gallery_wp_footer()
{
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
	<script>
	var pic_opacity = '<?php print (get_option(KINO_PLUGIN_SHORT_NAME."_pic_opacity"))?(get_option(KINO_PLUGIN_SHORT_NAME."_pic_opacity")):(0.5); ?>';
	var pic_fade = <?php print (get_option(KINO_PLUGIN_SHORT_NAME."_pic_fade"))?(get_option(KINO_PLUGIN_SHORT_NAME."_pic_fade")):(500); ?>;
	</script>
	<script src="<?php print KINO_PLUGIN_PATH; ?>/js/main.js"></script>
	<?php
}

// DIRTY TRICK - BUT IT WORKS
$kino_gallery_counter = 1;

/** 
* TITLE FILTER - ADDS THE CATEGORY NAME TO THE TITLE IF IN A GALLERY CATEGORY
*/
function kino_gallery_the_title_filter($title = "")
{
	global $post, $wpdb, $kino_gallery_counter;
	
	$_wpdb = $wpdb;
	
	if((is_category() || is_archive()) && $post->post_type == "pictures" && in_the_loop())
	{
		// IF CATEGORY WE GET THE CHANCE TO MODIFY THE POSTS ARRAY? ONLY WANT ONE POST!
		if($kino_gallery_counter > 1)
		{
			return "";
		}
		
		/*$term = wp_get_post_terms($post->ID,"picture_category");
		
		$description = "";
		if($term[0]->description)
		{
			$description = wpautop($term[0]->description);
		}
		
		//$title = "<h2>".$term[0]->name."</h2>".$description."<h3>".$title."</h3>";*/
	}
	
	return $title;	
}

/** 
* DATE FILTER - JUST STRIPS OUT THE DATE WHEN IT NEEDS TO
*/
function kino_gallery_the_date_filter($date = "")
{
	global $post, $wpdb, $kino_gallery_counter;
	
	$_wpdb = $wpdb;
	
	if((is_single() || is_category() || is_archive()) && $post->post_type == "pictures" && in_the_loop())
	{
		// IF CATEGORY WE GET THE CHANCE TO MODIFY THE POSTS ARRAY? ONLY WANT ONE POST!
		if($kino_gallery_counter > 1 || 1 == 1)
		{
			return "";
		}
	}
	
	return $date;	
}

/** 
* DATE FILTER - JUST STRIPS OUT THE DATE WHEN IT NEEDS TO
*/
function kino_gallery_the_time_filter($time = "")
{
	global $post, $wpdb, $kino_gallery_counter;
	
	$_wpdb = $wpdb;
	
	if((is_single() || is_category() || is_archive()) && $post->post_type == "pictures" && in_the_loop())
	{
		// IF CATEGORY WE GET THE CHANCE TO MODIFY THE POSTS ARRAY? ONLY WANT ONE POST!
		if($kino_gallery_counter > 1 || 1 == 1)
		{
			return "";
		}
	}
	
	return $time;	
}

/** 
* CONTENT FILTER
* USED TO CHECK IF WE ARE IN THE PICTURES POST OR CATEGORY AND MODIFY ACCORDINGLY
*/

function kino_gallery_the_content_filter($content)
{
	global $post, $wpdb, $kino_gallery_counter;
	
	$_wpdb = $wpdb;
	
	$width = 620;
	
	// IF SINGLE POST PAGE
	if(is_single($post->ID) && $post->post_type == "pictures")
	{
		
		$pic_meta = get_post_custom($post->ID);
		$featured_image = $pic_meta['_picture_url'][0];
		$content = '
		<div><a href="'. get_permalink($post->ID).'" title="'.$post->post_title.'"><img src="'.$featured_image.'" width="'.$width.'" height="'.$height.'" alt="'.$post->post_title.'" /></a></div>
		<br/>' . $content;
		
	}
	elseif(1==2)
	//elseif((is_category() || is_archive()) && $post->post_type == "pictures")
	{		
		// IF CATEGORY WE GET THE CHANCE TO MODIFY THE POSTS ARRAY? ONLY WANT ONE POST!
		if($kino_gallery_counter > 1)
		{
			return "";
		}
		
		$kino_gallery_counter++;
		
		$term = wp_get_post_terms($post->ID,"picture_category");
		
		// GET FIRST PRODUCT FROM THIS CATEGORY
		$query = "SELECT * from $wpdb->terms AS t
		INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id 
		INNER JOIN $wpdb->term_relationships AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id 
		INNER JOIN $wpdb->posts AS p ON tr.object_id = p.ID 
		
		WHERE p.post_status = 'publish' 
		AND p.post_type = 'pictures' 
		AND t.term_id = '".$term[0]->term_id."' 
		ORDER BY p.post_date DESC
		";
			
		$pic = $_wpdb->get_results($query);
		
		if($pic)
		{
			$pic_meta = get_post_custom($pic[0]->ID);
			$featured_image = $pic_meta['_picture_url'][0];
			$content = '
			<div><a href="'.get_permalink($pic[0]->ID).'" title="'.$pic[0]->post_title.'"><img src="'.$featured_image.'" width="'.$width.'" height="'.$height.'" alt="'.$post->post_title.'" /></a></div>
			<br/>';
			
			$content .= wpautop($pic[0]->post_content);
		}
	}
	return $content;
}

function kino_gallery_plugin_options()
{
	include "settings.php";	
}

function kino_gallery_plugin_menu()
{
	$page_title = KINO_PLUGIN_LONG_NAME;
	$menu_title = KINO_PLUGIN_LONG_NAME;
	$capability = "manage_options";
	$menu_slug = KINO_PLUGIN_SHORT_NAME."-settings";
	$function = "kino_gallery_plugin_options";
	
	add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
}

function kino_gallery_the_posts_filter($posts)
{
	global $wpdb;
	$new_posts = array($posts[0]);
	return $new_posts;
}

add_filter("the_title","kino_gallery_the_title_filter");
add_filter("the_date","kino_gallery_the_date_filter");
add_filter("the_time","kino_gallery_the_time_filter");
add_filter("the_content","kino_gallery_the_content_filter");

add_action('widgets_init', create_function('', 'return register_widget("KinoGallery");'));
add_action('init', 'kino_gallery_register_post_type_pictures');
add_action("admin_init", "kino_gallery_meta_init");
add_action('save_post', 'kino_gallery_save_post_type_pictures');
//add_action('init', 'kino_gallery_post_type_pictures_create_taxonomies');
add_action("wp_head", "kino_gallery_wp_head" );
add_action("wp_footer", "kino_gallery_wp_footer" );
add_action('admin_menu', 'kino_gallery_plugin_menu');
add_action('admin_head', "kino_gallery_admin_head");
?>