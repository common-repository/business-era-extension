<?php 

if ( ! function_exists( 'business_era_team_widget' ) ) :

    /**
     * Load widget.
     *
     * @since 1.0.0
     */
    function business_era_team_widget() {

        register_widget( 'Business_Era_Our_Team_Widget' );

    }

endif;

add_action( 'widgets_init', 'business_era_team_widget' );

if ( ! class_exists( 'Business_Era_Our_Team_Widget' ) ) :

    /**
     * Our Team widget class.
     *
     * @since 1.0.0
     */
    class Business_Era_Our_Team_Widget extends WP_Widget {

        function __construct() {
            $opts = array(
                'classname'   => 'business_era_widget_our_team',
                'description' => __( 'Our Team Widget', 'business-era-extension' ),
            );

            parent::__construct( 'business-era-our-team', esc_html__( 'Business Era: Our Team', 'business-era-extension' ), $opts );
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

            $team_args = array(
                'post_type'      => 'team',
                'posts_per_page' => esc_attr( $post_number ),
                'no_found_rows'  => true,
                );
            if ( absint( $post_category ) > 0 ) {

                $team_args['tax_query'] = array(
                                                array(
                                                    'taxonomy' => 'team-categories',
                                                    'field'    => 'term_id',
                                                    'terms'    => absint( $post_category ),
                                                ),
                                            );
            }

            $team_query = new WP_Query( $team_args );

            if ( $team_query->have_posts() ) : ?>

              <div class="our-team-widget our-team-col-<?php echo esc_attr( $post_column ); ?>">

                <div class="inner-wrapper">

                    <?php 
                    while ( $team_query->have_posts() ) :

                        $team_query->the_post(); ?>

                        <div class="our-team-item">
                            <div class="our-team-wrapper">

                            <?php if ( 'disable' !== $featured_image && has_post_thumbnail() ) :  ?>
                              <div class="our-team-thumb">
                                <a href="<?php the_permalink(); ?>">
                                    <?php
                                    the_post_thumbnail( esc_attr( $featured_image ) );
                                    ?>
                                </a>
                              </div><!-- .our-team-thumb -->
                            <?php endif; ?>

                            <div class="our-team-text-wrap">
                                  <h3 class="our-team-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                  </h3><!-- .our-team-title -->

                                <div class="our-team-meta">
                                    <?php 
                                    $post_id    = get_the_ID();

                                    $position   = get_post_meta( absint( $post_id ), 'position', true );
                                    $email      = get_post_meta( absint( $post_id ), 'email', true );
                                    $facebook   = get_post_meta( absint( $post_id ), 'facebook', true );
                                    $twitter    = get_post_meta( absint( $post_id ), 'twitter', true );
                                    $gplus      = get_post_meta( absint( $post_id ), 'gplus', true );
                                    $linkedin   = get_post_meta( absint( $post_id ), 'linkedin', true );
                                    $instagram  = get_post_meta( absint( $post_id ), 'instagram', true );
                                    ?>
                                    <?php if( !empty( $position ) ){ ?>
                                        <span class="our-team-position">
                                            <?php echo esc_html( $position ); ?>
                                        </span>
                                    <?php } ?>
                                    <?php if( !empty( $email ) ){ ?>
                                        <span class="our-team-email">
                                            <a href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                                        </span>
                                    <?php } ?>
                                    
                                    <ul class="our-team-social">
                                        <?php if( !empty( $facebook ) ){ ?>
                                            <li class="team-facebook">
                                                <a href="<?php echo esc_url( $facebook ); ?>"><span class="screen-reader-text"><?php _e('facebook', 'business-era-extension'); ?></span><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                            </li>
                                        <?php } ?>

                                        <?php if( !empty( $twitter ) ){ ?>
                                            <li class="team-twitter">
                                                <a href="<?php echo esc_url( $twitter ); ?>"><span class="screen-reader-text"><?php _e('twitter', 'business-era-extension'); ?></span><i class="fa fa-twitter" aria-hidden="true"></i></a>
                                            </li>
                                        <?php } ?>

                                        <?php if( !empty( $gplus ) ){ ?>
                                            <li class="team-gplus">
                                                <a href="<?php echo esc_url( $gplus ); ?>"><span class="screen-reader-text"><?php _e('google+', 'business-era-extension'); ?></span><i class="fa fa-google-plus" aria-hidden="true"></i></a>
                                            </li>
                                        <?php } ?>

                                        <?php if( !empty( $linkedin ) ){ ?>
                                            <li class="team-linkedin">
                                                <a href="<?php echo esc_url( $linkedin ); ?>"><span class="screen-reader-text"><?php _e('linkedin', 'business-era-extension'); ?></span><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                                            </li>
                                        <?php } ?>

                                        <?php if( !empty( $instagram ) ){ ?>
                                            <li class="team-instagram">
                                                <a href="<?php echo esc_url( $instagram ); ?>"><span class="screen-reader-text"><?php _e('instagram', 'business-era-extension'); ?></span><i class="fa fa-instagram" aria-hidden="true"></i></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div><!-- .our-team-meta -->
                            </div><!-- .our-team-text-wrap -->
                            </div>
                        </div>

                        <?php 
                    endwhile; 

                    wp_reset_postdata(); ?>

                </div><!-- .row -->

              </div><!-- .our-team-widget -->

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
                    'taxonomy'        => 'team-categories',
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