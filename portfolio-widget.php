<?php 

if ( ! function_exists( 'business_era_portfolio_widget' ) ) :

    /**
     * Load widget.
     *
     * @since 1.0.0
     */
    function business_era_portfolio_widget() {

        register_widget( 'Business_Era_Portfolio_Widget' );

    }

endif;

add_action( 'widgets_init', 'business_era_portfolio_widget' );

if ( ! function_exists( 'business_era_load_frontend_scripts' ) ) :

    /**
     * Load widgets scripts.
     *
     * @since 1.0.0
     */
    function business_era_load_frontend_scripts() {
        
        wp_enqueue_script( 'jquery-mixitup', plugins_url( 'assets/jquery.mixitup.min.js', __FILE__ ), array( 'jquery' ), '1.5.5' );

        wp_enqueue_script( 'business-era-filter', plugins_url( 'assets/filter.js', __FILE__ ), array( 'jquery-mixitup' ), '1.0.0' );
    }

endif;
    
add_action( 'wp_enqueue_scripts', 'business_era_load_frontend_scripts' );


if ( ! class_exists( 'Business_Era_Portfolio_Widget' ) ) :

    /**
     * Portfolio widget class.
     *
     * @since 1.0.0
     */
    class Business_Era_Portfolio_Widget extends WP_Widget {

        function __construct() {
            $opts = array(
                'classname'   => 'business_era_widget_portfolio',
                'description' => __( 'Portfolio Widget', 'business-era-extension' ),
            );

            parent::__construct( 'business-era-portfolio', esc_html__( 'Business Era: Portfolio', 'business-era-extension' ), $opts );
        }


        function widget( $args, $instance ) {

            $title             = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
            $post_category     = ! empty( $instance['post_category'] ) ? $instance['post_category'] : 0;
            $post_column       = ! empty( $instance['post_column'] ) ? $instance['post_column'] : 4;
            $featured_image    = ! empty( $instance['featured_image'] ) ? $instance['featured_image'] : 'medium';
            $post_number       = ! empty( $instance['post_number'] ) ? $instance['post_number'] : 4;

            echo $args['before_widget'];

            echo '<div class="container">';

            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            $portfolio_args = array(
                                'post_type'      => 'portfolio',
                                'posts_per_page' => esc_attr( $post_number ),
                                'no_found_rows'  => true,
                            );
            if ( absint( $post_category ) > 0 ) {
                $portfolio_args['tax_query'] = array(
                                                array(
                                                    'taxonomy' => 'portfolio-categories',
                                                    'field'    => 'term_id',
                                                    'terms'    => absint( $post_category ),
                                                ),
                                            );
            }

            $portfolio_query = new WP_Query( $portfolio_args );
            
            if ( $portfolio_query->have_posts() ) : ?>

              <div class="portfolio-widget portfolio-col-<?php echo esc_attr( $post_column ); ?>">

                <div class="inner-wrapper">
                    
                    <?php

                    $taxonomy = 'portfolio-categories';
                    $terms = get_terms($taxonomy); // Get all terms of a taxonomy

                    if ( $terms && !is_wp_error( $terms ) ) :
                    ?>
                        <ul id="filter-list">
                             <li class="filter" data-filter="all">All</li>
                            <?php foreach ( $terms as $term ) { ?>
                               <li class="filter" data-filter="<?php echo $term->slug; ?>"><?php echo $term->name; ?></li>
                            <?php } ?>
                        </ul>
                    <?php endif;?>
                    <div id="portfolio">
                        <?php 
                        while ( $portfolio_query->have_posts() ) :

                            $portfolio_query->the_post();

                            $post_id    = get_the_ID();

                            $terms      = wp_get_post_terms( absint($post_id), 'portfolio-categories');

                            $portfolio_terms = '';

                            foreach ($terms as $term) {

                                $portfolio_terms .= $term->slug.' ';
                               
                            } ?>
                            <div class="portfolio-item <?php echo esc_html( $portfolio_terms ); ?>">
                                <div class="portfolio-wrapper">
                                <?php 
                                $portfolio_type = get_post_meta( absint($post_id), 'portfolio_type', true );
                                $project_link   = get_post_meta( absint($post_id), 'project_link', true );

                                $portfolio_link_opening = '';
                                $portfolio_link_closing = '</a>';

                                if( 'external' === $portfolio_type ){

                                    $portfolio_link_opening = '<a href="'.esc_url( $project_link ).'" target="_blank">';

                                } elseif( 'new_window' === $portfolio_type ){
                                    $portfolio_link_opening = '<a href="'.esc_url( get_permalink() ).'" target="_self">';
                                }

                                if ( 'disable' !== $featured_image && has_post_thumbnail() ) :  ?>
                                  <div class="portfolio-thumb">
                                    <?php
                                    echo $portfolio_link_opening;
                                    the_post_thumbnail( esc_attr( $featured_image ) );
                                    echo $portfolio_link_closing;
                                    ?>
                                    </a>
                                  </div><!-- .portfolio-thumb -->
                                <?php endif; ?>
                                <div class="portfolio-text-wrap">
                                      <h3 class="portfolio-title">
                                        <?php 
                                        echo $portfolio_link_opening;
                                        the_title();
                                        echo $portfolio_link_closing;
                                        ?>
                                      </h3><!-- .portfolio-title -->
                                </div><!-- .portfolio-text-wrap -->
                                </div>
                            </div> 

                            <?php 
                        endwhile; 

                        wp_reset_postdata(); ?>

                    </div><!-- #portfolio -->

                </div><!-- .row -->

              </div><!-- .portfolio-widget -->

            <?php endif; 

            echo '</div>';

            echo $args['after_widget'];

        }

        function update( $new_instance, $old_instance ) {

            $instance = $old_instance;

            $instance['title']          = sanitize_text_field( $new_instance['title'] );
            $instance['post_category']  = absint( $new_instance['post_category'] );
            $instance['post_number']    = absint( $new_instance['post_number'] );
            $instance['post_column']    = absint( $new_instance['post_column'] );
            $instance['featured_image'] = esc_attr( $new_instance['featured_image'] );

            return $instance;
        }

        function form( $instance ) {

            $instance = wp_parse_args( (array) $instance, array(
                'title'          => '',
                'post_category'  => '',
                'post_column'    => 4,
                'featured_image' => 'medium',
                'post_number'    => 4,
            ) );
            ?>
            <p>
              <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><strong><?php _e( 'Title:', 'business-era-extension' ); ?></strong></label>
              <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
            </p>
            <p>
              <label for="<?php echo  esc_attr( $this->get_field_id( 'post_category' ) ); ?>"><strong><?php _e( 'Select Category:', 'business-era-extension' ); ?></strong></label>
                <?php
                $cat_args = array(
                    'orderby'         => 'name',
                    'hide_empty'      => 0,
                    'taxonomy'        => 'portfolio-categories',
                    'name'            => $this->get_field_name( 'post_category' ),
                    'id'              => $this->get_field_id( 'post_category' ),
                    'class'           => 'widefat',
                    'selected'        => absint( $instance['post_category'] ),
                    'show_option_all' => __( 'All Categories','business-era-extension' ),
                  );
                wp_dropdown_categories( $cat_args );
                ?>
            </p>
            <p>
              <label for="<?php echo esc_attr( $this->get_field_id( 'post_number' ) ); ?>"><strong><?php _e( 'Number of Posts:', 'business-era-extension' ); ?></strong></label>
              <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_number' ) ); ?>" name="<?php echo  esc_attr( $this->get_field_name( 'post_number' ) ); ?>" type="number" value="<?php echo esc_attr( $instance['post_number'] ); ?>" min="1" />
            </p>
            <p>
              <label for="<?php echo esc_attr( $this->get_field_id( 'post_column' ) ); ?>"><strong><?php _e( 'Number of Columns:', 'business-era-extension' ); ?></strong></label>
                <?php
                $this->dropdown_post_columns( array(
                    'id'       => $this->get_field_id( 'post_column' ),
                    'name'     => $this->get_field_name( 'post_column' ),
                    'selected' => absint( $instance['post_column'] ),
                    )
                );
                ?>
            </p>
            <p>
              <label for="<?php echo esc_attr( $this->get_field_id( 'featured_image' ) ); ?>"><strong><?php _e( 'Select Image Size:', 'business-era-extension' ); ?></strong></label>
                <?php
                $this->dropdown_image_sizes( array(
                    'id'       => $this->get_field_id( 'featured_image' ),
                    'name'     => $this->get_field_name( 'featured_image' ),
                    'selected' => esc_attr( $instance['featured_image'] ),
                    )
                );
                ?>
            </p>
            <?php
        }

        function dropdown_post_columns( $args ) {
            $defaults = array(
                'id'       => '',
                'name'     => '',
                'selected' => 0,
            );

            $r = wp_parse_args( $args, $defaults );
            $output = '';

            $choices = array(
                '2' => 2,
                '3' => 3,
                '4' => 4,
            );

            if ( ! empty( $choices ) ) {

                $output = "<select name='" . esc_attr( $r['name'] ) . "' id='" . esc_attr( $r['id'] ) . "'>\n";
                foreach ( $choices as $key => $choice ) {
                    $output .= '<option value="' . esc_attr( $key ) . '" ';
                    $output .= selected( $r['selected'], $key, false );
                    $output .= '>' . esc_html( $choice ) . '</option>\n';
                }
                $output .= "</select>\n";
            }

            echo $output;
        }

        function dropdown_image_sizes( $args ) {
            $defaults = array(
                'id'       => '',
                'class'    => 'widefat',
                'name'     => '',
                'selected' => 0,
            );

            $r = wp_parse_args( $args, $defaults );
            $output = '';

            $choices = array(
                'business-era-blog' => esc_html__( 'Business Era Custom', 'business-era-extension' ),
                'thumbnail'         => esc_html__( 'Thumbnail', 'business-era-extension' ),
                'medium'            => esc_html__( 'Medium', 'business-era-extension' ),
                'large'             => esc_html__( 'Large', 'business-era-extension' ),
                'full'              => esc_html__( 'Full', 'business-era-extension' ),
            );

            if ( ! empty( $choices ) ) {

                $output = "<select name='" . esc_attr( $r['name'] ) . "' id='" . esc_attr( $r['id'] ) . "' class='" . esc_attr( $r['class'] ) . "'>\n";
                foreach ( $choices as $key => $choice ) {
                    $output .= '<option value="' . esc_attr( $key ) . '" ';
                    $output .= selected( $r['selected'], $key, false );
                    $output .= '>' . esc_html( $choice ) . '</option>\n';
                }
                $output .= "</select>\n";
            }

            echo $output;
        }
    }

endif;