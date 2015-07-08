<?php

add_action( 'admin_menu', 'pnjo_add_admin_menu' );
add_action( 'admin_init', 'pnjo_settings_init' );
add_action( 'update_option_pnjo_settings', 'sanitize_option_changes' );


function sanitize_option_changes() {
  $shortcodeDictionary = array(
    1 => "[panjopageview market=\"nsx-prime\"",
    2 => "[panjopageview market=\"aquariums\"",
    3 => "[panjopageview market=\"audi\"",
    4 => "[panjopageview market=\"audio-visual\"",
    5 => "[panjopageview market=\"belly-dancing\"",
    6 => "[panjopageview market=\"azbilliards\"",
    7 => "[panjopageview market=\"bmw\"",
    8 => "[panjopageview market=\"outdoors\"",
    9 => "[panjopageview market=\"gigposters-com\"",
    10 => "[panjopageview market=\"outdoors\"",
    11 => "[panjopageview market=\"mazda\"",
    12 => "[panjopageview market=\"fashion\"",
    13 => "[panjopageview market=\"horse-blab\"",
    14 => "[panjopageview market=\"motorcycles\"",
    15 => "[panjopageview market=\"porsche\"",
    16 => "[panjopageview market=\"radio-control\"",
    17 => "[panjopageview market=\"radio-control\"",
    18 => "[panjopageview market=\"skiing\"",
    19 => "[panjopageview market=\"inertia\"",
    20 => "[panjopageview market=\"tesla\"",
  );


  //options array under settings page
  $settingArray = get_option( 'pnjo_settings' );

  //options array under customize page
  $settingArraySidebar = get_option( 'widget_panjo' );

  //$numListings = array_values( $settingArray )[0];

  //santize numlistings
  $numListingsChosen = $settingArray['pnjo_text_field_0'];

  if ( $numListingsChosen < 0 || $numListingsChosen > 1000 || !is_numeric( $numListingsChosen ) ) {
    $settingArray['pnjo_text_field_0'] = 5;
    $settingArraySidebar[2]['widget_num'] = 5;
  }
  else{
    $settingArraySidebar[2]['widget_num'] = $numListingsChosen;
  }

  $marketplaceChosen = $settingArray['pnjo_select_field_0'];

  //update shortcode
  $settingArray['pnjo_shortcode_field'] = $shortcodeDictionary[$marketplaceChosen] . "]";   //"[panjopageview market=\"bikes\"]";


  //update option changes data in the database
  update_option( 'pnjo_settings', $settingArray, yes );
  update_option( 'widget_panjo', $settingArraySidebar, yes );

}


function pnjo_add_admin_menu(  ) {

  add_options_page( 'Panjo', 'Panjo', 'manage_options', 'panjo', 'panjo_options_page' );

}


function pnjo_settings_init(  ) {

  register_setting( 'pluginPage',
    'pnjo_settings',
    'pnjo_validate_input'
  );


  add_settings_section(
    'pnjo_pluginPage_section',
    __( 'Panjo Options', 'wordpress' ),
    'pnjo_settings_section_callback',
    'pluginPage'
  );


  add_settings_field(
    'pnjo_text_field_0',
    __( '# of listings to display in sidebar', 'wordpress' ),
    'pnjo_text_field_0_render',
    'pluginPage',
    'pnjo_pluginPage_section'
  );



  add_settings_field(
    'pnjo_select_field_0',
    __( 'marketplace', 'wordpress' ),
    'pnjo_select_field_0_render',
    'pluginPage',
    'pnjo_pluginPage_section'
  );



  add_settings_field(
    'pnjo_text_field_2',
    __( '', 'wordpress' ),
    'pnjo_text_field_2_render',
    'pluginPage',
    'pnjo_pluginPage_section'
  );

  add_settings_field(
    'pnjo_shortcode_field',
    __( 'your shortcode', 'wordpress' ),
    'pnjo_shortcode_field_render',
    'pluginPage',
    'pnjo_pluginPage_section'
  );


}

// method that validates options
// on the panjo settings page
function pnjo_validate_input( $input ) {


  // Create our array for storing the validated options
  $output = array();

  // Loop through each of the incoming options
  foreach ( $input as $key => $value ) {

    // Check to see if the current option has a value. If so, process it.
    if ( isset( $input[$key] ) ) {

      // Strip all HTML and PHP tags and properly handle quoted strings
      $output[$key] = strip_tags( stripslashes( $input[ $key ] ) );

    } // end if

  } // end foreach

  // Return the array processing any additional functions filtered by this action
  return apply_filters( 'sandbox_theme_validate_input_examples', $output, $input );
}



//renders the text box with number of listings to display
function pnjo_text_field_0_render(  ) {

  $options = get_option( 'pnjo_settings' );
?>


  <input type='text' name='pnjo_settings[pnjo_text_field_0]' value='<?php echo $options['pnjo_text_field_0']; ?>'>
  <h5>Changes how many listings our sidebar widget displays. Range (0-1000).</h5>
  <?php

}

// renders sub-panjo marketplace option
function pnjo_select_field_0_render(  ) {

  $options = get_option( 'pnjo_settings' );
?>

  <select name='pnjo_settings[pnjo_select_field_0]'>
    <option value='1' <?php selected( $options['pnjo_select_field_0'], 1 ); ?>>Acura NSX</option>
<!--     <option value='2' <?php selected( $options['pnjo_select_field_0'], 2 ); ?>>Aquariums</option>
 -->    <option value='3' <?php selected( $options['pnjo_select_field_0'], 3 ); ?>>Audi</option>
    <option value='4' <?php selected( $options['pnjo_select_field_0'], 4 ); ?>>Audio</option>
    <option value='5' <?php selected( $options['pnjo_select_field_0'], 5 ); ?>>Belly Dancing</option>
    <option value='6' <?php selected( $options['pnjo_select_field_0'], 6 ); ?>>Billiards</option>
    <option value='7' <?php selected( $options['pnjo_select_field_0'], 7 ); ?>>BMW</option>
    <option value='8' <?php selected( $options['pnjo_select_field_0'], 8 ); ?>>Camping</option>
    <option value='9' <?php selected( $options['pnjo_select_field_0'], 9 ); ?>>Concert Posters</option>
    <option value='10' <?php selected( $options['pnjo_select_field_0'], 10 ); ?>>Flashlights</option>
    <option value='11' <?php selected( $options['pnjo_select_field_0'], 11 ); ?>>Mazda</option>
    <option value='12' <?php selected( $options['pnjo_select_field_0'], 12 ); ?>>Men's Grooming</option>
    <option value='13' <?php selected( $options['pnjo_select_field_0'], 13 ); ?>>Model Horses</option>
    <option value='14' <?php selected( $options['pnjo_select_field_0'], 14 ); ?>>Motorcycles</option>
    <option value='15' <?php selected( $options['pnjo_select_field_0'], 15 ); ?>>Porsche</option>
    <option value='16' <?php selected( $options['pnjo_select_field_0'], 16 ); ?>>RC</option>
    <option value='17' <?php selected( $options['pnjo_select_field_0'], 17 ); ?>>Slot Cars</option>
    <option value='18' <?php selected( $options['pnjo_select_field_0'], 18 ); ?>>Skiing</option>
    <option value='19' <?php selected( $options['pnjo_select_field_0'], 19 ); ?>>Surfing</option>
    <option value='20' <?php selected( $options['pnjo_select_field_0'], 20 ); ?>>Tesla</option>
  </select>

<?php

}

// renders I want to add my marketplace link
function pnjo_text_field_2_render(  ) {

  $options = get_option( 'pnjo_settings' );
?>


  <h5>Don’t see the marketplace you’re interested in?
  </h5>

  <a href="mailto:support@panjo.com?subject=Please add my marketplace&body=Thank
 you for showing an interest in Panjo! Please take a moment to tell us about your marketplace. 
  %0D%0DWhat is your marketplace called?
  %0D%0DWhat kinds of items does your marketplace buy and sell? 
  %0D%0DIs there anything special you want to tell us about your marketplace?"
    >Create your own marketplace on Panjo for free. 
  </a>

  

  <?php

}

//renders the text box with number of listings to display
function pnjo_shortcode_field_render(  ) {

  $options = get_option( 'pnjo_settings' );
?>


<p>    <?php echo $options['pnjo_shortcode_field']; ?> </p>


  <h5>Add this shortcode to any wordpress page to dislay Panjo listings in a feed.
     <br>Click save changes to update the shortcode.
  </h5>
 <?php
}


//we could describe our option page here if we wanted to
function pnjo_settings_section_callback(  ) {
  //echo __( 'This section description', 'wordpress' );
}


function panjo_options_page(  ) {

?>
  <form action='options.php' method='post'>

    <?php
  settings_fields( 'pluginPage' );
  do_settings_sections( 'pluginPage' );
  submit_button();
?>

  </form>
  <?php

}

?>
