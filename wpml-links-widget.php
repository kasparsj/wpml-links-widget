<?php
/*
Plugin Name: WPML Links Widget
Version: 1.1
*/

/*
Important notice : In my WP installation, everything concerning 'link_category' taxonomy was broken, so I also had to modify every call to get_terms( 'link_category' ) to make it work. The solution was to add the actual taxonomy in a "taxonomy" field in the second array parameter, just as follow :

get_terms( 'link_category', array( 'taxonomy' => 'link_category' ) );
*/

add_action('widgets_init',  array('WPMLLinksWidget','register_widget'));
 
class WPMLLinksWidget extends WP_Widget
{
 
	function WPMLLinksWidget() {
 
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_links widget_wpml_links', 'description' => __('This widget allows you to set different link categories to display based on a language setting.', $this->plugin_name) );
 
		/* Widget control settings. */
 
		/* Create the widget. */
		$this->WP_Widget( 'WPMLLinksWidget', __('Multilingual Links'), $widget_ops );
 
	}
 
	function register_widget()
	{
		register_widget('WPMLLinksWidget');
	}
 
	function widget( $args, $instance ) {
 
		global $sitepress;
 
		if(class_exists('WP_Widget_Links')) {
 
			$link_widget = new WP_Widget_Links();
 
			if(isset($sitepress))
			{
 
				$lang = $sitepress->get_current_language();
				$instance['category'] = $instance[$lang.'_category'];
				$link_widget->widget($args, $instance);
 
			}
 
		}
 
	}
 
	function update($new_instance, $old_instance) {
		
		$new_instance = (array) $new_instance;
		$instance = array( 'images' => 0, 'name' => 0, 'description' => 0, 'rating' => 0);
		foreach ( $instance as $field => $val ) {
			if ( isset($new_instance[$field]) )
				$instance[$field] = 1;
		}
 
		$langs = $this->GetLangs();
		foreach($langs as $lang=>$lang_id)
		{
			$instance[$lang.'_category'] = intval($new_instance[$lang.'_category']);
		}
 
		return $instance;
 
	}
 
	function form($instance) {
 
		$settings = $this->GetSettings();
		$langs = $this->GetLangs();
 
		$instance = wp_parse_args( (array) $instance, $settings );
 
		$link_cats = get_terms( 'link_category', array( 'taxonomy' => 'link_category' ) );
		
		foreach($langs as $lang=>$lang_id)
		{
			echo '
 
<br /><label for="'. $this->get_field_id($lang.'_category').'">'. __('Select Link Category') . ' (' .$lang.'): </label><br />';
			echo '
<select id="'. $this->get_field_id($lang.'_category').'" name="'. $this->get_field_name($lang.'_category').'">';
			foreach ( $link_cats as $link_cat ) {
 
				echo '<option value="' . intval($link_cat->term_id) . '" '; 
				echo ($link_cat->term_id == $instance[$lang.'_category']) ? ' selected="selected"' : '';
				echo '>' . $link_cat->name . "</option>
 
\n";
			}
			echo '</select>
 
';
 
		}
		?>
 <br />
<input class="checkbox" type="checkbox" <?php checked($instance['images'], true) ?> id="<?php echo $this->get_field_id('images'); ?>" name="<?php echo $this->get_field_name('images'); ?>" />
		<label for="<?php echo $this->get_field_id('images'); ?>"><?php _e('Show Link Image'); ?></label><br />
<input class="checkbox" type="checkbox" <?php checked($instance['name'], true) ?> id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" />
		<label for="<?php echo $this->get_field_id('name'); ?>"><?php _e('Show Link Name'); ?></label><br />
<input class="checkbox" type="checkbox" <?php checked($instance['description'], true) ?> id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" />
		<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Show Link Description'); ?></label><br />
<input class="checkbox" type="checkbox" <?php checked($instance['rating'], true) ?> id="<?php echo $this->get_field_id('rating'); ?>" name="<?php echo $this->get_field_name('rating'); ?>" />
		<label for="<?php echo $this->get_field_id('rating'); ?>"><?php _e('Show Link Rating'); ?></label><br />
 
		<?php
 
	}
 
	function GetLangs()
	{
		global $sitepress_settings;
		$langs = $sitepress_settings['default_categories'];
		return $langs;
	}
 
	function GetSettings()
	{
		$settings = array();
 
		$settings['images'] = true;
		$settings['name'] = true;
		$settings['description'] = false;
		$settings['rating'] = false;
		$langs = $this->GetLangs();
		foreach($langs as $lang=>$lang_id)
		{
			$settings[$lang.'_category'] = '';
		}
		return $settings;
	}
 
}
?>