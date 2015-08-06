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

class Panjo extends WP_Widget {
	//process the new widget
	public function __construct() {
		$option = array(
			'classname' => 'Panjo',
			'description' => 'Display listings from the marketplace for enthusiasts.'
		);
		$this->WP_Widget( 'Panjo', 'Panjo', $option );
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
		insertStyle();

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

		//gets the array of settings
		$setting = get_option( 'pnjo_settings' );

		//number of items to display
		$limit = array_values( $setting )[0];

		//which market to display listings from
		$marketChoice = array_values( $setting )[1];

		//display listings on sidebar
		displayListingsOnSideBar( $choicesDictionary[$marketChoice], $limit );
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

	shuffle($feed);

	//iterate through listings found and echo display to sidebar
	for ( $x=0;$x<$maxListings;$x++ ) {
		$title = str_replace( ' & ', ' &amp; ', $feed[$x]['title'] );
		$link = $feed[$x]['link'];
		$description = $feed[$x]['desc'];
		$date = date( 'l F d, Y', strtotime( $feed[$x]['date'] ) );
		$imageNode = $feed[$x]['imageNode'];
		$linkthumb = $imageNode->item( 0 )->getAttribute( 'url' );
		$itemPrice = $feed[$x]['price'];

		//format title
		$title = shortenTitle( $title );

		//actually display the listing
		?>
		<div class="col-md-12 panjo-listing">
			<?php
			displayListing( $link, $linkthumb, $title, $itemPrice );//$tempPrice );
			?>
		</div>
		<?php
	}
}

function insertStyle(){
	?>

	<!-- internal html stylesheet -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link href='//fonts.googleapis.com/css?family=Open+Sans:700,400' rel='stylesheet' type='text/css'>

	<style>
		div.panjo-listing{
			background-color: white;
			text-align: center;
			margin: 5px;
			box-shadow: 0 0 1px 1px #E0E0E0;
		}
		div.panjo-text-wrapper{
			height: auto;
		}
		div.panjo-text{
			height: 47px;
			overflow-y: auto;
			line-height: 100%;
		}
		div.panjo-corners{
			height: 30px;
		}
		div.panjo-image-wrapper{
			margin-bottom: 5px;
		}
		a.panjo-title{
			font-family: 'Open Sans', sans-serif;
			font-weight: bold;
			color: black !important;
			text-align: center;
			border-bottom: none;
			font-size: 12px;
		}
		p.panjo-price{
			font-size: 16px;
			margin: 0px;
			position: absolute;
			bottom: 5px;
			left: 10px;
			color: black;
			font-weight: bold;
		}
		img.panjo-logo{
			position: absolute;
			bottom: 5px;
			right: 5px;
			width: 25px;
			height: 25px;
		}
	</style>
	<?php
}

// returns the html for displaying listings in a grid view on a page
function getListingsForPageView( $chosenMarket ) {
	insertStyle();

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

	//gets the array of settings
	$setting = get_option( 'pnjo_settings' );

	//number of items to display
	$limit = array_values( $setting )[0];

	shuffle($feed);

	//iterate through listings and get their content (using ob) to return then display
	$num_feed = count($feed);
	if ($limit > $num_feed) {
		$limit = $num_feed;
	}

	for ( $x=0;$x<$limit;$x++ ) {
		$title = str_replace( ' & ', ' &amp; ', $feed[$x]['title'] );
		$link = $feed[$x]['link'];
		$description = $feed[$x]['desc'];
		$date = date( 'l F d, Y', strtotime( $feed[$x]['date'] ) );
		$imageNode = $feed[$x]['imageNode'];
		$linkthumb = $imageNode->item( 0 )->getAttribute( 'url' );
		$itemPrice = $feed[$x]['price'];
		$title = shortenTitle( $title );

		//records output to later return to shortcode function
		ob_start();
		?>
		<div class="col-sm-4 col-md-3 panjo-listing ">
		<?php
		// echo $title;
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
	$sourceLink =  "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$escaped_link = htmlspecialchars($sourceLink, ENT_QUOTES, 'UTF-8');
	$utm_postfix = "?utm_source=" . $escaped_link . "&utm_medium=wordpress&utm_campaign=wpplugin";
	$link = $link . $utm_postfix;

	?>
	<div class ="panjo-image-wrapper">
		<a href= <?php echo $link; ?> >
			<img src=<?php echo $linkthumb; ?> width="100%"/>
		</a>
	</div>
	<div class="panjo-text-wrapper">
		<div class ="panjo-text">
			<?php echo '<a href="'.$link.'" class="panjo-title" >'.$title.'</a><br />';?>
		</div>
			<div class="panjo-corners">
				<p class="panjo-price"><?php echo $tempPrice; ?></p>
				<?php echo '<img class ="panjo-logo" src="http://www.panjo.com/Content/images/favicon.ico?v=3">' ?>
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