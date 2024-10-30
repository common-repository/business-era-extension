<?php 
if ( ! function_exists('business_era_team') ) {

	// Register Custom Post Type
	function business_era_team() {

		$labels = array(
			'name'                  => _x( 'Team', 'Post Type General Name', 'business-era-extension' ),
			'singular_name'         => _x( 'Team', 'Post Type Singular Name', 'business-era-extension' ),
			'menu_name'             => __( 'Team', 'business-era-extension' ),
			'name_admin_bar'        => __( 'Team', 'business-era-extension' ),
			'archives'              => __( 'Item Archives', 'business-era-extension' ),
			'attributes'            => __( 'Item Attributes', 'business-era-extension' ),
			'parent_item_colon'     => __( 'Parent Item:', 'business-era-extension' ),
			'all_items'             => __( 'All Items', 'business-era-extension' ),
			'add_new_item'          => __( 'Add New Item', 'business-era-extension' ),
			'add_new'               => __( 'Add New', 'business-era-extension' ),
			'new_item'              => __( 'New Item', 'business-era-extension' ),
			'edit_item'             => __( 'Edit Item', 'business-era-extension' ),
			'update_item'           => __( 'Update Item', 'business-era-extension' ),
			'view_item'             => __( 'View Item', 'business-era-extension' ),
			'view_items'            => __( 'View Items', 'business-era-extension' ),
			'search_items'          => __( 'Search Item', 'business-era-extension' ),
			'not_found'             => __( 'Not found', 'business-era-extension' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'business-era-extension' ),
			'featured_image'        => __( 'Featured Image', 'business-era-extension' ),
			'set_featured_image'    => __( 'Set featured image', 'business-era-extension' ),
			'remove_featured_image' => __( 'Remove featured image', 'business-era-extension' ),
			'use_featured_image'    => __( 'Use as featured image', 'business-era-extension' ),
			'insert_into_item'      => __( 'Insert into item', 'business-era-extension' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'business-era-extension' ),
			'items_list'            => __( 'Items list', 'business-era-extension' ),
			'items_list_navigation' => __( 'Items list navigation', 'business-era-extension' ),
			'filter_items_list'     => __( 'Filter items list', 'business-era-extension' ),
		);
		$args = array(
			'label'                 => __( 'Team', 'business-era-extension' ),
			'description'           => __( 'Post type to create team', 'business-era-extension' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 10,
			'menu_icon'             => 'dashicons-groups',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,		
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( 'team', $args );

	}

}

add_action( 'init', 'business_era_team', 0 );

if ( ! function_exists( 'business_era_team_category' ) ) {

	// Add Category to Custom Post Type
	function business_era_team_category() {
	   
	    $args = array( 
	        'label'                 => __( 'Categories', 'business-era-extension' ),
	        'public'                => true,
	        'show_in_nav_menus'     => true,
	        'show_ui'               => true,        
	        'show_admin_column'     => true,
	        'show_in_admin_bar'     => true,  
	        'hierarchical'          => true,
	        'rewrite'               => array('slug' => 'team-categories'),
	        'query_var'             => true
	    );

	    register_taxonomy( 'team-categories', 'team', $args );

	}

}

add_action( 'init', 'business_era_team_category', 0 );


/**********************************************************
* Add Extra Custom Fields to the Post Type Add / Edit screen
* Plus Update Method
**********************************************************/

add_action( 'admin_init', 'business_era_team_meta_init' );
add_action( 'save_post', 'business_era_team_meta_save' );

function business_era_team_meta_init() {

    add_meta_box("team-information", __( 'Member Information', 'business-era-extension' ), "business_era_meta_options", "team", "normal", "high");      
}

function business_era_meta_options( $post ) {

    $values 	= get_post_custom( $post->ID );

    $position  	= isset( $values['position'] ) ? esc_html( $values['position'][0] ) : '';

    $email      = isset( $values['email'] ) ? esc_html( $values['email'][0] ) : '';

    $facebook   = isset( $values['facebook'] ) ? esc_url( $values['facebook'][0] ) : '';

    $twitter   	= isset( $values['twitter'] ) ? esc_url( $values['twitter'][0] ) : '';

    $gplus   	= isset( $values['gplus'] ) ? esc_attr( $values['gplus'][0] ) : '';

    $linkedin   = isset( $values['linkedin'] ) ? esc_attr( $values['linkedin'][0] ) : '';

    $instagram  = isset( $values['instagram'] ) ? esc_attr( $values['instagram'][0] ) : '';


    wp_nonce_field( 'business_era_meta_box_nonce', 'meta_box_nonce' );

    ?>

    <table width="100%" border="0" class="options" cellspacing="5" cellpadding="5">
        <tr>
            <td width="1%">
                <label for="position"><?php _e('Position', 'business-era-extension'); ?></label>
            </td>
            <td width="10%">
                <input type="text" id="position" class="widefat" name="position" value="<?php echo esc_html( $position ); ?>" placeholder="<?php esc_html_e('Enter position like Manager, Developer, Accountant', 'business-era-extension'); ?>"/>
            </td>          
        </tr>  
        <tr>
            <td width="1%">
                <label for="email"><?php _e('Email', 'business-era-extension'); ?></label>
            </td>
            <td width="10%">
                <input type="text" id="email" class="widefat" name="email" value="<?php echo esc_html( $email ); ?>" placeholder="<?php esc_html_e('Enter email address', 'business-era-extension'); ?>"/>
            </td>          
        </tr>  
        <tr>
            <td width="1%">
                <label for="facebook"><?php _e('Facebook', 'business-era-extension'); ?></label>
            </td>
            <td width="10%">
                <input type="text" id="facebook" class="widefat" name="facebook" value="<?php echo esc_url( $facebook ); ?>" placeholder="<?php esc_html_e('Enter facebook url', 'business-era-extension'); ?>" />
            </td>          
        </tr>
        <tr>
            <td width="1%">
                <label for="twitter"><?php _e('Twitter', 'business-era-extension'); ?></label>
            </td>
            <td width="10%">
                <input type="text" id="twitter" class="widefat" name="twitter" value="<?php echo esc_url( $twitter ); ?>" placeholder="<?php esc_html_e('Enter twitter url', 'business-era-extension'); ?>" />
            </td>          
        </tr> 
        <tr>
            <td width="1%">
                <label for="gplus"><?php _e('Google +', 'business-era-extension'); ?></label>
            </td>
            <td width="10%">
                <input type="text" id="gplus" class="widefat" name="gplus" value="<?php echo esc_url( $gplus ); ?>" placeholder="<?php esc_html_e('Enter google + url', 'business-era-extension'); ?>" />
            </td>          
        </tr> 
        <tr>
            <td width="1%">
                <label for="linkedin"><?php _e('LinkedIn', 'business-era-extension'); ?></label>
            </td>
            <td width="10%">
                <input type="text" id="linkedin" class="widefat" name="linkedin" value="<?php echo esc_url( $linkedin ); ?>" placeholder="<?php esc_html_e('Enter linkedin url', 'business-era-extension'); ?>" />
            </td>          
        </tr> 
        <tr>
            <td width="1%">
                <label for="instagram"><?php _e('Instagram', 'business-era-extension'); ?></label>
            </td>
            <td width="10%">
                <input type="text" id="instagram" class="widefat" name="instagram" value="<?php echo esc_url( $instagram ); ?>" placeholder="<?php esc_html_e('Enter instagram url', 'business-era-extension'); ?>" />
            </td>          
        </tr>
       
          
    </table>   
    <?php   
}


function business_era_team_meta_save( $post_id )
{
    global $post;  

    $custom_meta_fields = array( 'position', 'email', 'facebook', 'twitter', 'gplus', 'linkedin', 'instagram');

    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'business_era_meta_box_nonce' ) ) return;
    
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;
    
    // now we can actually save the data
    $allowed = array( 
                'em' 		=> array(),
                'strong' 	=> array(),
                'span' 		=> array(),
            );    
 
    foreach( $custom_meta_fields as $custom_meta_field ){

        if( isset( $_POST[$custom_meta_field] ) )           

            update_post_meta($post->ID, $custom_meta_field, wp_kses( $_POST[$custom_meta_field], $allowed) );      
    }
        
   
}