<?php 

if ( ! function_exists( 'business_era_testimonials_widget' ) ) :

    /**
     * Load widget.
     *
     * @since 1.0.0
     */
    function business_era_testimonials_widget() {

        register_widget( 'Business_Era_Testimonials_Widget' );

    }

endif;

add_action( 'widgets_init', 'business_era_testimonials_widget' );

if ( ! class_exists( 'Business_Era_Testimonials_Widget' ) ) :

    /**
     * Our Team widget class.
     *
     * @since 1.0.0
     */
    class Business_Era_Testimonials_Widget extends WP_Widget {

        function __construct() {
            $opts = array(
                'classname'   => 'business_era_widget_testimonials',
                'description' => __( 'Testimonials Widget', 'business-era-extension' ),
            );

            parent::__construct( 'business-era-testimonials', esc_html__( 'Business Era: Testimonials', 'business-era-extension' ), $opts );
        }


        function widget( $args, $instance ) {

            $title             = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
            $post_category     = ! empty( $instance['post_category'] ) ? $instance['post_category'] : 0;
            $post_number       = ! empty( $instance['post_number'] ) ? $instance['post_number'] : 4;


            $transition_effects = !empty( $instance['transition_effects'] )? $instance['transition_effects'] : '';
            $transition_delay   = !empty( $instance['transition_delay'] )? $instance['transition_delay'] : 3;

            $show_caption       = ! empty( $instance['show_caption'] ) ? $instance['show_caption'] : 0;

            $show_arrow         = ! empty( $instance['show_arrow'] ) ? $instance['show_arrow'] : 0;

            $show_pager         = ! empty( $instance['show_pager'] ) ? $instance['show_pager'] : 0;

            $enable_autoplay    = ! empty( $instance['enable_autoplay'] ) ? $instance['enable_autoplay'] : 0;

            $bg_pic             = ! empty( $instance['bg_pic'] ) ? esc_url_raw( $instance['bg_pic'] ) : '';

            // Add background image.
            if ( ! empty( $bg_pic ) ) {
                $background_style = '';
                $background_style .= ' style="background-image:url(' . esc_url( $bg_pic ) . ');" ';
                $args['before_widget'] = implode( $background_style . ' ' . 'class="with_bg ', explode( 'class="', $args['before_widget'], 2 ) );
            }

            echo $args['before_widget'];

            echo '<div class="container">';

            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            $testimonials_args = array(
                                    'post_type'      => 'testimonials',
                                    'posts_per_page' => esc_attr( $post_number ),
                                    'no_found_rows'  => true,
                                );
            if ( absint( $post_category ) > 0 ) {
                $testimonials_args['tax_query'] = array(
                                                    array(
                                                        'taxonomy' => 'testimonials-categories',
                                                        'field'    => 'term_id',
                                                        'terms'    => absint( $post_category ),
                                                    ),
                                                );
            }

            $testimonials_query = new WP_Query( $testimonials_args );
             
            if ( $testimonials_query->have_posts() ) : ?>

              <div class="testimonials-widget">

                <div class="inner-wrapper">
                <?php 

                if ( 1 === $enable_autoplay ) {
                    $timeout = 1000 * absint( $transition_delay ); // Change seconds to miliseconds.
                }
                else {
                    $timeout = 0;
                }

                ?>
                <div class="cycle-slideshow" id="testimonial-slider" data-cycle-fx="<?php echo esc_attr( $transition_effects ); ?>" data-cycle-speed="1000" data-cycle-pause-on-hover="true" data-cycle-loader="true" data-cycle-log="false" data-cycle-swipe="true" data-cycle-auto-height="container" data-cycle-timeout="<?php echo esc_attr( $timeout ); ?>" data-cycle-slides="article" data-cycle-caption-template='<h3><a href="{{url}}">{{title}}</a></h3><p>{{excerpt}}</p>' data-cycle-pager-template='<span class="pager-box"></span>'>
                   
                   <?php 
                    if ( 1 === $show_arrow ) : ?>
                            <div class="cycle-next"><i class="fa fa-angle-right" aria-hidden="true"></i></div>
                            <div class="cycle-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></div>
                    <?php endif; ?>

                    <?php while ( $testimonials_query->have_posts() ) : 

                            $testimonials_query->the_post(); ?>

                            <article data-cycle-title="<?php the_title(); ?>" data-cycle-url="<?php the_permalink(); ?>" data-cycle-excerpt="<?php echo get_the_content(); ?>">
                                <div class="testimonials-wrap">
                                    <?php
                                    if ( 1 === $show_caption ) : ?>
                                        <div class="testimonials-caption">
                                            <?php the_content(); ?>
                                        </div> 
                                    <?php
                                    endif;

                                    if( has_post_thumbnail() ){ ?>
                                        <figure>
                                          <?php the_post_thumbnail( 'thumbnail' ); ?>  
                                        </figure>
                                    <?php } ?>
                                        
                                    <div class="testimonials-meta">
                                        <span class="testimonial-title"><?php the_title(); ?></span>
                                        <?php
                                        $post_id    = get_the_ID();
                                        $position   = get_post_meta( absint($post_id), 'position', true );
                                        $company    = get_post_meta( absint($post_id), 'company', true );

                                        if( !empty( $position ) ){
                                            echo '<span class="position">'.esc_html( $position ).' @ </span>';

                                        }
                                        if( !empty( $company ) ){
                                            echo '<span class="company">'.esc_html( $company ).'</span>';

                                        } ?>
                                    </div>
                                </div>
                            </article>

                    <?php endwhile; 

                    wp_reset_postdata();

                    if ( 1 === $show_pager ) : ?>
                        <div class="cycle-pager"></div>
                    <?php endif; ?>

                </div>


                </div><!-- .inner-wrapper -->

              </div><!-- .testimonials-widget -->

            <?php endif; 

            echo '</div>';

            echo $args['after_widget'];

        }

        function update( $new_instance, $old_instance ) {
            
            $instance = $old_instance;

            $instance['title']              = sanitize_text_field( $new_instance['title'] );
            $instance['post_category']      = absint( $new_instance['post_category'] );
            $instance['post_number']        = absint( $new_instance['post_number'] );

            $instance['transition_effects'] = esc_attr( $new_instance['transition_effects'] );
            $instance['transition_delay']   = absint( $new_instance['transition_delay'] );
            $instance['show_caption']       = (bool) $new_instance['show_caption'] ? 1 : 0;
            $instance['show_arrow']         = (bool) $new_instance['show_arrow'] ? 1 : 0;
            $instance['show_pager']         = (bool) $new_instance['show_pager'] ? 1 : 0;
            $instance['enable_autoplay']    = (bool) $new_instance['enable_autoplay'] ? 1 : 0;
            $instance['bg_pic']             = esc_url_raw( $new_instance['bg_pic'] );

            return $instance;
        }

        function form( $instance ) {

            $instance = wp_parse_args( (array) $instance, array(
                'title'                 => '',
                'post_category'         => '',
                'post_number'           => 4,
                'transition_effects'    => 'scrollHorz',
                'transition_delay'      => 3,
                'show_caption'          => 1,
                'show_arrow'            => 1,
                'show_pager'            => 1,
                'enable_autoplay'       => 1,
                'bg_pic'                => '',
                
            ) );

            $show_caption      = isset( $instance['show_caption'] ) ? (bool) $instance['show_caption'] : 0;
            $show_arrow        = isset( $instance['show_arrow'] ) ? (bool) $instance['show_arrow'] : 0;
            $show_pager        = isset( $instance['show_pager'] ) ? (bool) $instance['show_pager'] : 0;
            $enable_autoplay   = isset( $instance['enable_autoplay'] ) ? (bool) $instance['enable_autoplay'] : 0;

            $bg_pic = '';

            if ( ! empty( $instance['bg_pic'] ) ) {

                $bg_pic = $instance['bg_pic'];

            }

            $wrap_style = '';

            if ( empty( $bg_pic ) ) {

                $wrap_style = ' style="display:none;" ';
            }

            $image_status = false;

            if ( ! empty( $bg_pic ) ) {
                $image_status = true;
            }

            $delete_button = 'display:none;';

            if ( true === $image_status ) {
                $delete_button = 'display:inline-block;';
            }
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
                    'taxonomy'        => 'testimonials-categories',
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
              <label for="<?php echo esc_attr( $this->get_field_id( 'transition_effects' ) ); ?>"><strong><?php _e( 'Transition Effect:', 'business-era-extension' ); ?></strong></label>
                <?php
                $this->dropdown_transition_effect( array(
                    'id'       => $this->get_field_id( 'transition_effects' ),
                    'name'     => $this->get_field_name( 'transition_effects' ),
                    'selected' => esc_attr( $instance['transition_effects'] ),
                    )
                );
                ?>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'transition_delay' ) ); ?>"><strong><?php _e( 'Transition Delay:', 'business-era-extension' ); ?></strong></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'transition_delay' ) ); ?>" name="<?php echo  esc_attr( $this->get_field_name( 'transition_delay' ) ); ?>" type="number" value="<?php echo esc_attr( $instance['transition_delay'] ); ?>" min="1" />
                <small><?php _e( 'in seconds', 'business-era-extension' ); ?></small>
            </p>

            <p>
                <input class="checkbox" type="checkbox" <?php checked( $show_caption ); ?> id="<?php echo $this->get_field_id( 'show_caption' ); ?>" name="<?php echo $this->get_field_name( 'show_caption' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'show_caption' ); ?>"><?php _e( 'Show Caption', 'business-era-extension' ); ?></label>
            </p>

            <p>
                <input class="checkbox" type="checkbox" <?php checked( $show_arrow ); ?> id="<?php echo $this->get_field_id( 'show_arrow' ); ?>" name="<?php echo $this->get_field_name( 'show_arrow' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'show_arrow' ); ?>"><?php _e( 'Show Arrow', 'business-era-extension' ); ?></label>
            </p>

            <p>
                <input class="checkbox" type="checkbox" <?php checked( $show_pager ); ?> id="<?php echo $this->get_field_id( 'show_pager' ); ?>" name="<?php echo $this->get_field_name( 'show_pager' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'show_pager' ); ?>"><?php _e( 'Show Pager', 'business-era-extension' ); ?></label>
            </p>

            <p>
                <input class="checkbox" type="checkbox" <?php checked( $enable_autoplay ); ?> id="<?php echo $this->get_field_id( 'enable_autoplay' ); ?>" name="<?php echo $this->get_field_name( 'enable_autoplay' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'enable_autoplay' ); ?>"><?php _e( 'Enable Autoplay', 'business-era-extension' ); ?></label>
            </p>
            <div class="cover-image">
                <label for="<?php echo esc_attr( $this->get_field_id( 'bg_pic' ) ); ?>">
                    <strong><?php esc_html_e( 'Background Image:', 'business-era-extension' ); ?></strong>
                </label>
                <input type="text" class="img widefat" name="<?php echo esc_attr( $this->get_field_name( 'bg_pic' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'bg_pic' ) ); ?>" value="<?php echo esc_url( $instance['bg_pic'] ); ?>" />
                <div class="rtam-preview-wrap" <?php echo $wrap_style; ?>>
                    <img src="<?php echo esc_url( $bg_pic ); ?>" alt="<?php _e( 'Preview', 'business-era-extension' ); ?>" />
                </div><!-- .rtam-preview-wrap -->
                <input type="button" class="select-img button button-primary" value="<?php esc_html_e( 'Upload', 'business-era-extension' ); ?>" data-uploader_title="<?php esc_html_e( 'Select Background Image', 'business-era-extension' ); ?>" data-uploader_button_text="<?php esc_html_e( 'Choose Image', 'business-era-extension' ); ?>" />
                <input type="button" value="<?php echo _x( 'X', 'Remove Button', 'business-era-extension' ); ?>" class="button button-secondary btn-image-remove" style="<?php echo esc_attr( $delete_button ); ?>" />
            </div>
            <?php
        }

        function dropdown_transition_effect( $args ) {
            $defaults = array(
                'id'       => '',
                'class'    => 'widefat',
                'name'     => '',
                'selected' => 0,
            );

            $r = wp_parse_args( $args, $defaults );
            $output = '';

            $choices = array(
                'fade'       => esc_html__( 'fade', 'business-era-extension' ),
                'fadeout'    => esc_html__( 'fadeout', 'business-era-extension' ),
                'none'       => esc_html__( 'none', 'business-era-extension' ),
                'scrollHorz' => esc_html__( 'scrollHorz', 'business-era-extension' ),
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