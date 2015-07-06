<?php
include 'settings.php';

/*
  Plugin Name: Panjo
  Plugin URL:
  Description: Panjo Widget
  Version: 1.0
  Author: Max Crane, Issac
  Author URI:
  License: GPLv2
 */

add_action( 'widgets_init', 'panjo_add_stylesheet' );


class Panjo extends WP_Widget {

  //process the new widget
  public function __construct() {
    $option = array(
      'classname' => 'Panjo',
      'description' => 'Display listings from the marketplace for enthusiasts.'
    );
    $this->WP_Widget( 'Panjo', 'Panjo', $option );
  }

  //build the widget settings form
  function form( $instance ) {
    $default =  array( 'numItems' => 5 );
    $instance = wp_parse_args( (array) $instance, $default );

    $num_id = $this->get_field_id( 'widget_num' );
    $num_name = $this->get_field_name( 'widget_num' );

    $num_items = "";

    if ( isset( $instance['widget_num'] ) ) { $num = $instance['widget_num']; }


    echo "\r\n".'<p><label for="'.$num_id.'">'.__( 'Number of Items', 'ebay-feeds-for-wordpress'  ).': <input type="text" class="widefat" id="'.$num_id.'" name="'.$num_name .'" value="'.esc_attr( $num ).'" /><label></p>';

  }

  //save the widget settings
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['widget_num'] = strip_tags( $new_instance['widget_num'] );

    $settingArray = get_option( 'pnjo_settings' );

    if ( $instance['widget_num'] < 0 || $instance['widget_num'] > 1000 || !is_numeric( $instance['widget_num'] ) ) {
      $instance['widget_num'] = 5;
      $settingArray['pnjo_text_field_0'] = 5;
    }
    else {
      $settingArray['pnjo_text_field_0'] =  $instance['widget_num'];
    }

    update_option( 'pnjo_settings', $settingArray, yes );
    return $instance;
  }


  //display the widget
  function widget( $args, $instance ) {

    $choicesDictionary = array(
      1 => "http://www.panjo.com/rss/partnerfeed/nsx-prime",
      2 => "http://www.panjo.com/rss/partnerfeed/panjo-aquariums",
      3 => "http://www.panjo.com/rss/partnerfeed/panjo-audi",
      4 => "http://www.panjo.com/rss/partnerfeed/panjo-audio-visual",
      5 => "http://www.panjo.com/rss/partnerfeed/panjo-belly-dancing",
      6 => "http://www.panjo.com/rss/partnerfeed/azbilliards",
      7 => "http://www.panjo.com/rss/partnerfeed/panjo-bmw",
      8 => "http://www.panjo.com/rss/partnerfeed/panjo-outdoors",
      9 => "http://www.panjo.com/rss/partnerfeed/gigposters-com",
      10 => "http://www.panjo.com/rss/partnerfeed/panjo-outdoors",
      11 => "http://www.panjo.com/rss/partnerfeed/panjo-mazda",
      12 => "http://www.panjo.com/rss/partnerfeed/panjo-fashion",
      13 => "http://www.panjo.com/rss/partnerfeed/model-horse-blab",
      14 => "http://www.panjo.com/rss/partnerfeed/panjo-motorcycles",
      15 => "http://www.panjo.com/rss/partnerfeed/panjo-porsche",
      16 => "http://www.panjo.com/rss/partnerfeed/panjo-radio-control",
      17 => "http://www.panjo.com/rss/partnerfeed/panjo-radio-control",
      18 => "http://www.panjo.com/rss/partnerfeed/panjo-skiing",
      19 => "http://www.panjo.com/rss/partnerfeed/inertia",
      20 => "http://www.panjo.com/rss/partnerfeed/panjo-tesla",
    );

    echo $before_widget;
    //echo "test";
    //print_r(get_option( 'pnjo_settings' ));
    //echo 'This is the Panjo widget';
?>


        <!-- internal html stylesheet -->
        <style>
        a {
          color: black;
        }
        h1 {
          margin-left: 10px;
        }

        div.listing{
          background-color: white;
          text-align: center;
          font-family: 'Open Sans',sans-serif;
          font-size: 10pt;
          padding: 0px;
          margin-left: auto;
          margin-right: auto;
          margin-bottom: 15px;
          box-shadow: 0 0 1px 1px rgba(0,0,0,.05);
          max-width: 220px;
          line-height: 115%;
          /*border-style: solid;
          border-color: white;
          border-width: 15px;*/
          position: relative;
        }
        div.text{
          min-height: 60px;
        }
        p{
          color: black;
          text-align: center;
          margin-top:  1px;
          margin-bottom: 0em;
        }
        p.title{
          color: black;
          text-align: center;
          border-bottom: none;
          font-size: 12px;
        }
        p.price{
          font-size: 14px;
          margin: 0px;
          position: absolute;
          bottom: 10px;
          left: 5px;
        }

        img.logo{
          position: absolute;
          bottom: 5px;
          right: 5px;
          width: 25px;
          height: 25px;
        }
        .column{
          float: left;
          width: 220px;
          padding: 5px;
          overflow: hidden;
        }
        </style>



        <?php

    //gets the array of settings
    $setting = get_option( 'pnjo_settings' );

    //number of items to display
    $limit = array_values( $setting )[0];

    //which market to display listings from
    $marketChoice = array_values( $setting )[1];

    //display listings on sidebar
    displayListingsOnSideBar( $choicesDictionary[$marketChoice], $limit );

    echo $after_widget;
  }

}

function displayListingsOnSideBar( $rssUrl, $maxListings ) {


  $feed = getItemsFromRssUrl( $rssUrl );

  //number of listings found on feed
  $listingsFound = count( $feed );

  //guard so we don't try to load mroe listings than we found
  if ( $maxListings> $listingsFound ) {
    $maxListings = $listingsFound;
  }


  //iterate through listings found and echo display to sidebar
  for ( $x=0;$x<$maxListings;$x++ ) {
    $title = str_replace( ' & ', ' &amp; ', $feed[$x]['title'] );
    $link = $feed[$x]['link'];
    $description = $feed[$x]['desc'];
    $imageurl = $feed[$x]['imageurl'];
    $date = date( 'l F d, Y', strtotime( $feed[$x]['date'] ) );
    $imageNode = $feed[$x]['imageNode'];
    $linkthumb = $imageNode->item( 0 )->getAttribute( 'url' );
    $itemPrice = $feed[$x]['price'];

    //format title
    $title = shortenTitle( $title );

    //actually display the listing
    displayListing( $link, $linkthumb, $title, $itemPrice );//$tempPrice );
  }
}

// returns the html for displaying listings in a grid view on a page
function getListingsForPageView( $chosenMarket ) {

  $shortcodeDictionary = array(
    "nsx-prime" => "http://www.panjo.com/rss/partnerfeed/nsx-prime",
    "aquariums" => "http://www.panjo.com/rss/partnerfeed/panjo-aquariums",
    "audi" => "http://www.panjo.com/rss/partnerfeed/panjo-audi",
    "audio-visual" => "http://www.panjo.com/rss/partnerfeed/panjo-audio-visual",
    "belly-dancing" => "http://www.panjo.com/rss/partnerfeed/panjo-belly-dancing",
    "azbilliards" => "http://www.panjo.com/rss/partnerfeed/azbilliards",
    "bmw" => "http://www.panjo.com/rss/partnerfeed/panjo-bmw",
    "outdoors" => "http://www.panjo.com/rss/partnerfeed/panjo-outdoors",
    "gigposters-com" => "http://www.panjo.com/rss/partnerfeed/gigposters-com",
    "mazda" => "http://www.panjo.com/rss/partnerfeed/panjo-mazda",
    "fashion" => "http://www.panjo.com/rss/partnerfeed/panjo-fashion",
    "horse-blab" => "http://www.panjo.com/rss/partnerfeed/model-horse-blab",
    "motorcycles" => "http://www.panjo.com/rss/partnerfeed/panjo-motorcycles",
    "porsche" => "http://www.panjo.com/rss/partnerfeed/panjo-porsche",
    "radio-control" => "http://www.panjo.com/rss/partnerfeed/panjo-radio-control",
    "skiing" => "http://www.panjo.com/rss/partnerfeed/panjo-skiing",
    "inertia" => "http://www.panjo.com/rss/partnerfeed/inertia",
    "tesla" => "http://www.panjo.com/rss/partnerfeed/panjo-tesla",
  );

  $toReturn = "";

  $feed = getItemsFromRssUrl( $shortcodeDictionary[$chosenMarket] );

  //number of listings found on feed
  $listingsFound = count( $feed );

  //iterate through listings and get their content (using ob) to return then display
  for ( $x=0;$x<$listingsFound;$x++ ) {
    $title = str_replace( ' & ', ' &amp; ', $feed[$x]['title'] );
    $link = $feed[$x]['link'];
    $description = $feed[$x]['desc'];
    $imageurl = $feed[$x]['imageurl'];
    $date = date( 'l F d, Y', strtotime( $feed[$x]['date'] ) );
    $imageNode = $feed[$x]['imageNode'];
    $linkthumb = $imageNode->item( 0 )->getAttribute( 'url' );
    $itemPrice = $feed[$x]['price'];

    $title = shortenTitle( $title );

    //records output to later return to shortcode function
    ob_start();
?>
      <div class = "column">
        <?php
    displayListing( $link, $linkthumb, $title, $itemPrice );
?>
      </div>
    <?php

    //append output for that specific listing to return
    $toReturn = $toReturn . ob_get_clean();
  }

  return $toReturn;
}

function displayListing( $link, $linkthumb, $title, $tempPrice ) {
?>
  <div class ="listing">
    <div class = "image-wrapper">
      <a href= <?php echo $link; ?> >
        <img class = "thumbnail" src=<?php echo $linkthumb; ?> width="100%"/>
      </a>
    </div>
    <div class ="text">
      <?php echo '<p class ="title"><strong><a href="'.$link.'" title="'.$title.'">'.$title.'</a></strong><br /></p>';?>
      <p class="price"><?php echo $tempPrice; ?></p>
      <?php echo '<img class ="logo" src="http://www.panjo.com/Content/images/favicon.ico?v=3">' ?>
    </div>
  </div>


<?php
}


// gets the actual data from the RSS feed off Panjo's website
function getItemsFromRssUrl( $theUrl ) {

  $rss = new DOMDocument();
  $rss->load( $theUrl );
  $feed = array();

  foreach ( $rss->getElementsByTagName( 'item' ) as $node ) {
    $item = array (
      'title' => $node->getElementsByTagName( 'title' )->item( 0 )->nodeValue,
      'desc' => $node->getElementsByTagName( 'description' )->item( 0 )->nodeValue,
      'link' => $node->getElementsByTagName( 'link' )->item( 0 )->nodeValue,
      'date' => $node->getElementsByTagName( 'pubDate' )->item( 0 )->nodeValue,
      'imageNode' => $node->getElementsByTagName( 'image' ),
      'price' => $node->getElementsByTagName( 'price' )->item( 0 )->nodeValue,
    );

    array_push( $feed, $item );
  }

  return $feed;
}

//shortens title of item if over 50 characters so it will fit in the item box
function shortenTitle( $title ) {
  $maxChars = 50;

  if ( strlen( $title ) > $maxChars ) {
    $title = str_split( $title, $maxChars );
    $title = $title[0];
    $title = $title . "...";
  }

  return $title;
}

add_shortcode( 'panjopageview', 'pageview_func' );

// Shortcode function returns html & php to diplsay code on page
function pageview_func( $atts ) {
  $a = shortcode_atts( array(
      'market' => 'something',
    ), $atts );

  return getListingsForPageView( $a['market'] );
}

// register widget
add_action( 'widgets_init', 'panjo_register' );
function panjo_register() {
  register_widget( 'Panjo' );
}

//adds css style sheet
function panjo_add_stylesheet() {
  wp_register_style( 'panjo-style', plugins_url( 'panjo-style.css', __FILE__ ) );
  wp_enqueue_style( 'panjo-style' );
}

?>
