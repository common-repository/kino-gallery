<?php
$time_format_options = array("12","24");
$options = array (
	array("name" => "General",
		"type" => "heading"),
		
		array("name" => "Max width of full size image",
			"desc" => "Type width in pixels e.g. 620",
			"id" => KINO_PLUGIN_SHORT_NAME."_max_width",
			"std" => "620",
			"type" => "text"),
		
		array("name" => "Thumbnail size",
			"desc" => "Type width/height in pixels e.g. 58",
			"id" => KINO_PLUGIN_SHORT_NAME."_thumbnail_size",
			"std" => "58",
			"type" => "text"),
		/*
		array("name" => "Thumbnails per row",
			"desc" => "Number of thumbnails per row",
			"id" => KINO_PLUGIN_SHORT_NAME."_thumbnail_count",
			"std" => "3",
			"type" => "text"),*/
		
		array("name" => "Thumbnails CSS",
			"desc" => "Add custom styles to thumbs",
			"id" => KINO_PLUGIN_SHORT_NAME."_thumbnail_css",
			"std" => "",
			"type" => "text"),
		
		array("name" => "Opacity for thumbnails",
			"desc" => "Type value from 0 (invisible) to 1 (opaque)",
			"id" => KINO_PLUGIN_SHORT_NAME."_pic_opacity",
			"std" => "0.5",
			"type" => "text"),
		
		array("name" => "Fade speed for thumbnails",
			"desc" => "Eg. 500 for 5 milliseconds",
			"id" => KINO_PLUGIN_SHORT_NAME."_pic_fade",
			"std" => "500",
			"type" => "text"),
		
		/*
		array("name" => "Widget is category specific",
			"desc" => "",
			"id" => KINO_PLUGIN_SHORT_NAME."_widget_category_specific",
			"std" => "checked",
			"type" => "checkbox")*/

);
?>