<?php 
/**
 * Registers the People shortcodes
 * @author Jonathan Hendricker
 * @since 1.0.3.1
 * @param array $atts | Assoc. array of shortcode options
 * @return array
 **/

if ( !function_exists( 'ucf_people_cards' ) ) {
	
    function ucf_people_cards( $atts ){

        $atts = shortcode_atts( array(
            'layout'    => 'horizontal',
            'slug'      => '',
            'title'     => 'default',
            'email'     => 'no',
            'phone'     => 'no',
            'office'    => 'no',
            'link'      => 'no'
        ), $atts, 'ucf-people-cards' );

        if( $atts['slug'] !== '' ){
            $person_slug = str_replace(' ', '-', strtolower($atts['slug']));

            $args = array(
                'post_type'     => 'person',
                'name'          => $person_slug,
                'post_status'   => 'publish',
            );
            $person_query = new WP_Query( $args );

            if ( $person_query->have_posts() ): 
                $person_query->the_post();

                $person_name = get_field('person_title_prefix').' '.get_the_title().' '.get_field('person_title_suffix');
                
                $person_title = ($atts['title'] !== 'default') ? $atts['title'] : get_field('person_jobtitle'); 

                if($atts['email'] === 'yes') $person_email = get_field('person_email');
                if($atts['office']=== 'yes') $person_office = get_field('person_room');

                if( $atts['phone'] === 'yes'){
                    $person_phone = array();
                    if(have_rows('person_phone_numbers')):
                        while( have_rows('person_phone_numbers')): the_row();
                            $current_number = get_sub_field('number');
                            array_push( $person_phone, $current_number );
                        endwhile;
                    endif;
                }

                $person_link = ($atts['link'] === 'yes') ?get_site_url().'/person/'.$person_slug : '';

                ob_start();
            
                echo '<div class="person_card_'.$atts['layout'].' ">';
                echo "<div class='person_card_photo media-background-container person-photo rounded-circle'>"; 
                echo ($person_link !== '') ? "<a href='$person_link'>".get_the_post_thumbnail( get_the_ID(), 'medium', array('class' => 'media-background object-fit-cover') )."</a>" : the_post_thumbnail( 'medium', array('class' => 'media-background object-fit-cover') ) ; 
                echo "</div><div class='person_card_info'> ";
                
                echo ($person_link !== '') ? "<h3><a href='$person_link'>$person_name</a></h3>" : "<h3>$person_name</h3>";

                echo "<h5>$person_title</h5><p>";

                if(!empty($person_email)) 
                    echo "<a href='mailto:".$person_email."'>".$person_email."</a><br/>";

                if( $atts['phone'] === 'yes'){
                    foreach( (array) $person_phone as $number ){ echo $number.'<br/>'; }
                }

                if(!empty($person_office))
                    echo $person_office;

                echo '</p></div></div>';
                return ob_get_clean();
            else:
                return "<p>No person can be found with that name.</p>";
            endif;

        }
    }
    add_shortcode( 'ucf-people-card', 'ucf_people_cards');
}

if ( ! function_exists( 'ucf_people_card_shortcode_interface' ) ) {
	function ucf_people_card_shortcode_interface( $shortcodes ) {
        if ( class_exists( 'WP_SCIF_Config' ) ) {
            $settings = array(
                'command' => 'ucf-people-card',
                'name'    => 'UCF People Card',
                'desc'    => 'Displays a person\'s photo and basic information from the People section.',
                'content' => false,
                'preview' => false,
                'fields'  => array(
                    array(
                        'param'    => 'slug',
                        'name'     => 'Slug',
                        'desc'     => 'A lowercase version of the person\'s first and last name, with any spaces repalced by dashes - <br/>E.g. Bob Smith would be bob-smith.',
                        'type'     => 'text'
                    ),
                    array(
                        'param'    => 'title',
                        'name'     => 'Title',
                        'desc'     => 'The job title to display below the person\'s name. If left blank, the title from their People page will be displayed.',
                        'type'     => 'text'
                    ),
                    array(
                        'param'    => 'layout',
                        'name'     => 'Layout',
                        'desc'     => 'The layout used to display the person\'s image and information. Horizontal is side by side and Vertical is stacked.',
                        'type'     => 'select',
                        'options'  => array('horizontal' => 'Horizontal', 'vertical' => 'Vertical')
                    ),
                    array(
                        'param'    => 'email',
                        'name'     => 'Email address',
                        'desc'     => 'Whether or not to show the email address.',
                        'type'     => 'select',
                        'options'  => array('yes' => 'Yes','no' => 'No')
                    ),
                    array(
                        'param'    => 'phone',
                        'name'     => 'Phone Number',
                        'desc'     => 'Whether or not to show any phone numbers.',
                        'type'     => 'select',
                        'options'  => array('yes' => 'Yes','no' => 'No')
                    ),
                    array(
                        'param'    => 'office',
                        'name'     => 'Office Number',
                        'desc'     => 'Whether or not to show the office number.',
                        'type'     => 'select',
                        'options'  => array('yes' => 'Yes','no' => 'No')
                    ),
                    array(
                        'param'    => 'link',
                        'name'     => 'Link',
                        'desc'     => 'Whether or not to have the photo and name link to the person\'s People page.',
                        'type'     => 'select',
                        'options'  => array('yes' => 'Yes','no' => 'No')
                    ),
                ),
                'group'	=> 'UCF People Card'
            );

            $shortcodes[] = $settings;

            return $shortcodes;
        }
	}
}

// Register shortcodes and shortcode interface options
add_shortcode( 'ucf-people-cards' , array( 'UCF_People_Shortcode', 'cards_shortcode' ) );