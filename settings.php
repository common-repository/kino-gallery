<?php
include "plugin-options.php";

if ( $_GET['page'] == KINO_PLUGIN_SHORT_NAME."-settings")
{
	if ( 'save' == $_REQUEST['action'] )
	{
		foreach ($options as $value)
		{
			//print $_REQUEST[ $value['id'] ];
			update_option( $value['id'], $_REQUEST[ $value['id'] ] );
		}
		foreach ($options as $value)
		{
			if( isset( $_REQUEST[ $value['id'] ] ) )
			{
				update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
			}
			else
			{
				delete_option( $value['id'] );
			}
		}
		
		/*
		if(isset($_REQUEST["kinogallery_category_page"]))
		{
			
			update_option( "kinogallery_category_page", $_REQUEST[ "kinogallery_category_page"]  );
		}
		else
		{
			delete_option( "kinogallery_category_page" );
		}
		*/
			
		
		//header("Location: ".get_bloginfo("url")."/wp-admin/options-general.php?page=".KINO_PLUGIN_SHORT_NAME."-settings&saved=true");
		//exit;
	}
	else if( 'reset' == $_REQUEST['action'] )
	{
		foreach ($options as $value)
		{
			delete_option( $value['id'] );
		}
		//header("Location: ".get_bloginfo("url")."/wp-admin/options-general.php?page=".KINO_PLUGIN_SHORT_NAME."-settings&reset=true");
		//exit;
	}
}

if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.KINO_PLUGIN_LONG_NAME.' settings saved.</strong></p></div>';
if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.KINO_PLUGIN_LONG_NAME.' settings reset.</strong></p></div>';
?>

<div class="wrap" >
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
		<h2><?php echo KINO_PLUGIN_LONG_NAME; ?> Settings</h2>
		<div id="poststuff" class="metabox-holder">
			<p class="submit">
				<input name="save" type="submit" value="Save changes" />
				<input type="hidden" name="action" value="save" />
			</p>    
			<table class="widefat" >
				<?php
				foreach ($options as $value)
				{
					switch ( $value['type'] )
					{
						case "text" :  ?>
							<tr>
								<th scope="row"><?php echo $value['name']; ?></th>
								<td>
								<input style="width:500px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
								<br /><small><?php echo $value['desc']; ?></small>
								</td>
							</tr>
							<?php 
							break;
						
						case "color" :  ?>
							<tr>
								<th scope="row"><?php echo $value['name']; ?></th>
								<td>
								<input style="width:500px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
								<div class="fl colorSelector" id="colorSelector-<?php echo $value['id']; ?>"><div style="background-color: <?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>"></div></div>
								<br /><small><?php echo $value['desc']; ?></small>
								</td>
							</tr>
							<?php 
							break;
							
						case "textarea" :  
							?>
							<tr valign="top">
								<th scope="row"><?php echo $value['name']; ?>:</th>
								<td>
								<textarea style="width:500px;height:100px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" ><?php
								if( get_option($value['id']) != "") {
									echo stripslashes(get_option($value['id']));
								  }else{
									echo $value['std'];
								}?></textarea>
								<br /><?php echo $value['desc']; ?>
								</td>
							</tr>
							<?php 
							break;
						case "checkbox" : 
							?>
							<tr valign="top">
								<th scope="row"><?php echo $value['name']; ?></th>
								<td>
								<?php if(get_option($value['id'])){
								$checked = "checked=\"checked\"";
								  }else{
								$checked = "";
								}
								?>
								<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
								<?php echo $value['desc']; ?>
								</td>
							</tr>
							<?php 
							break;
						case  "select": 
							?>
							<tr>
								<th scope="row"><?php echo $value['name']; ?></th>
								<td>
								<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
								<?php foreach ($value['options'] as $option) { ?>
								<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
								<?php } ?>
								</select>
								<br /><small><?php echo $value['desc']; ?></small>
								</td>
							</tr>        
							<?php 
							break;
						case "heading" :
							?>
							<thead>
								<tr valign="top">
								<th colspan="2">
								<?php echo $value['name']; ?>
								</th>
								</tr>
							</thead>
							<?php
							break;
					}  
				}
				/*
				// GO THROUGH ANY CATEGORIES ADDED FOR EVENTS
				$categories = get_terms("picture_category", "hide_empty=0"); 
				if(count($categories) > 0)
				{
					?>
					<thead>
						<tr valign="top">
						<th colspan="2">
						Category Options
						</th>
						</tr>
					</thead>
					<?php
					// GET OPTIONS
					$page_cat = get_option("kinogallery_category_page");
					print_r($page_cat);
					foreach($categories as $x)
					{
						// GET OPTION FOR THIS IF EXISTS
						//${"{KINO_PLUGIN_SHORT_NAME}_cat_{$x->term_id}_color"} = get_option({"{KINO_PLUGIN_SHORT_NAME}_cat_{$x->term_id}_color"});
						//$ec_cat_color[$x->term_id] = get_option("kinogallery_category_page_".$x->term_id);
						?>
						<tr>
							<th scope="row">'<?php echo $x->name; ?>' page ID</th>
							<td>
								<input name="kinogallery_category_page[<?php print $x->term_id; ?>]" id="kinogallery_category_page_<?php print $x->term_id; ?>" type="text" value="<?php print $page_cat[$x->term_id]; ?>" />
							</td>
						</tr>
						<?php
					}
				}
				*/
				?>
			</table>
			<p class="submit">
				<input name="save" type="submit" value="Save changes" />
				<input type="hidden" name="action" value="save" />
			</p>        
		</div>
	</form>
</div>