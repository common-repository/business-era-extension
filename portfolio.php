<?php 
if ( ! function_exists('business_era_portfolio') ) {

	// Register Custom Post Type
	function business_era_portfolio() {

		$labels = array(
			'name'                  => _x( 'Portfolio', 'Post Type General Name', 'business-era-extension' ),
			'singular_name'         => _x( 'Portfolio', 'Post Type Singular Name', 'business-era-extension' ),
			'menu_name'             => __( 'Portfolio', 'business-era-extension' ),
			'name_admin_bar'        => __( 'Portfolio', 'business-era-extension' ),
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
			'label'                 => __( 'Portfolio', 'business-era-extension' ),
			'description'           => __( 'Post type to create portfolio', 'business-era-extension' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 10,
			'menu_icon'             => 'dashicons-external',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,		
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( 'portfolio', $args );

	}

}

add_action( 'init', 'business_era_portfolio', 0 );

if ( ! function_exists( 'business_era_portfolio_category' ) ) {

	// Add Category to Custom Post Type
	function business_era_portfolio_category() {
	   
	    $args = array( 
	        'label'                 => __( 'Categories', 'business-era-extension' ),
	        'public'                => true,
	        'show_in_nav_menus'     => true,
	        'show_ui'               => true,        
	        'show_admin_column'     => true,
	        'show_in_admin_bar'     => true,  
	        'hierarchical'          => true,
	        'rewrite'               => array('slug' => 'portfolio-categories'),
	        'query_var'             => true
	    );

	    register_taxonomy( 'portfolio-categories', 'portfolio', $args );

	}

}

add_action( 'init', 'business_era_portfolio_category', 0 );


/**********************************************************
* Add Extra Custom Fields to the Post Type Add / Edit screen
* Plus Update Method
**********************************************************/

add_action( 'admin_init', 'business_era_portfolio_meta_init' );
add_action( 'save_post', 'business_era_portfolio_meta_save' );

function business_era_portfolio_meta_init() {

    add_meta_box("portfolio-information", __( 'Portfolio Details', 'business-era-extension' ), "business_era_portfolio_meta_options", "portfolio", "normal", "high");      
}

function business_era_portfolio_meta_options( $post ) {

    $values 		= get_post_custom( $post->ID );

    $portfolio_type = isset( $values['portfolio_type'] ) ? esc_html( $values['portfolio_type'][0] ) : '';

    $project_link  	= isset( $values['project_link'] ) ? esc_html( $values['project_link'][0] ) : '';


    wp_nonce_field( 'business_era_portfolio_meta_box_nonce', 'meta_box_nonce' );

    ?>

    <table width="100%" border="0" class="options" cellspacing="5" cellpadding="5">
        <tr>
            <td width="1%">
                <label for="portfolio_type"><?php _e('Portfolio Type', 'business-era-extension'); ?></label>
            </td>
            <td width="10%">
                <select id="portfolio_type" class="widefat" name="portfolio_type">
                	<option value="external" <?php selected( $portfolio_type, 'external' ); ?>><?php _e('On an external website', 'business-era-extension'); ?></option>
                	<option value="new_window" <?php selected( $portfolio_type, 'new_window' ); ?>><?php _e('On a separate portfolio page', 'business-era-extension'); ?></option>
                </select>
                
            </td>          
        </tr>  
        <tr>
            <td width="1%">
                <label for="project_link"><?php _e('Project Link', 'business-era-extension'); ?></label>
            </td>
            <td width="10%">
                <input type="text" id="project_link" class="widefat" name="project_link" value="<?php echo esc_url( $project_link ); ?>" placeholder="<?php esc_html_e('Enter link of project', 'business-era-extension'); ?>"/>
            </td>          
        </tr>          
    </table>   
    <?php  
}


function business_era_portfolio_meta_save( $post_id )
{
    global $post;  

    $custom_meta_fields = array( 'portfolio_type', 'project_link' );

    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'business_era_portfolio_meta_box_nonce' ) ) return;
    
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