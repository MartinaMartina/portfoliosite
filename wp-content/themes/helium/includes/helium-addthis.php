<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	External AddThis Services
============================================================================= */

if( ! function_exists( 'helium_addthis_external_services' ) ) : 

function helium_addthis_external_services() {
	return array(
		'facebook_like', 
		'tweet', 
		'google_plusone', 
		'linkedin_counter', 
		'stumbleupon_badge'
	);
}
endif;

/* ==========================================================================
	Available AddThis Services
============================================================================= */

if( ! function_exists( 'helium_addthis_services' ) ) : 

function helium_addthis_services() {
	return array_merge( helium_addthis_external_services(), array(
		'pinterest', 
		'compact', 
		'expanded', 
		'100zakladok', 
		'addressbar', 
		'adfty', 
		'adifni', 
		'advqr', 
		'amazonwishlist', 
		'amenme', 
		'aim', 
		'aolmail', 
		'apsense', 
		'atavi', 
		'baidu', 
		'balatarin', 
		'beat100', 
		'bitly', 
		'bizsugar', 
		'bland', 
		'blogger', 
		'blogkeen', 
		'blogmarks', 
		'bobrdobr', 
		'bonzobox', 
		'bookmarkycz', 
		'bookmerkende', 
		'box', 
		'buffer', 
		'camyoo', 
		'care2', 
		'foodlve', 
		'citeulike', 
		'cleanprint', 
		'cleansave', 
		'cloob', 
		'technerd', 
		'link', 
		'cosmiq', 
		'cssbased', 
		'delicious', 
		'diary_ru', 
		'digg', 
		'diggita', 
		'diigo', 
		'douban', 
		'draugiem', 
		'edcast', 
		'efactor', 
		'email', 
		'mailto', 
		'evernote', 
		'exchangle', 
		'stylishhome', 
		'fabulously40', 
		'facebook', 
		'messenger', 
		'facenama', 
		'informazione', 
		'thefancy', 
		'fashiolista', 
		'favable', 
		'faves', 
		'favorites', 
		'favoritus', 
		'financialjuice', 
		'flipboard', 
		'folkd', 
		'thefreedictionary', 
		'gg', 
		'gmail', 
		'govn', 
		'google', 
		'google_classroom', 
		'googleplus', 
		'googletranslate', 
		'google_plusone_share', 
		'hackernews', 
		'hatena', 
		'hedgehogs', 
		'historious', 
		'hootsuite', 
		'houzz', 
		'w3validator', 
		'indexor', 
		'instapaper', 
		'iorbix', 
		'jappy', 
		'kaixin', 
		'kakao', 
		'ketnooi', 
		'kik', 
		'kindleit', 
		'kledy', 
		'lidar', 
		'lineme', 
		'linkedin', 
		'linkuj', 
		'livejournal', 
		'mymailru', 
		'margarin', 
		'markme', 
		'meinvz', 
		'memonic', 
		'memori', 
		'mendeley', 
		'meneame', 
		'mixi', 
		'moemesto', 
		'mrcnetworkit', 
		'myspace', 
		'myvidster', 
		'n4g', 
		'naszaklasa', 
		'netvibes', 
		'netvouz', 
		'newsmeback', 
		'newsvine', 
		'nujij', 
		'nurses_lounge', 
		'odnoklassniki_ru', 
		'oknotizie', 
		'openthedoor', 
		'hotmail', 
		'oyyla', 
		'pafnetde', 
		'pdfmyurl', 
		'pinboard', 
		'pinterest_share', 
		'plurk', 
		'pocket', 
		'posteezy', 
		'print', 
		'printfriendly', 
		'pusha', 
		'qrsrc', 
		'quantcast', 
		'qzone', 
		'reddit', 
		'rediff', 
		'renren', 
		'researchgate', 
		'retellity', 
		'safelinking', 
		'scoopit', 
		'sinaweibo', 
		'skype', 
		'skyrock', 
		'slack', 
		'smiru', 
		'sms', 
		'sodahead', 
		'spinsnap', 
		'startaid', 
		'startlap', 
		'studivz', 
		'stuffpit', 
		'stumbleupon', 
		'stumpedia', 
		'supbro', 
		'surfingbird', 
		'svejo', 
		'symbaloo', 
		'taringa', 
		'telegram', 
		'tencentqq', 
		'tencentweibo', 
		'thisnext', 
		'trello', 
		'tuenti', 
		'tumblr', 
		'twitter', 
		'typepad', 
		'urlaubswerkde', 
		'viadeo', 
		'viber', 
		'virb', 
		'visitezmonsite', 
		'vk', 
		'vkrugudruzei', 
		'voxopolis', 
		'vybralisme', 
		'wanelo', 
		'internetarchive', 
		'sharer', 
		'wechat', 
		'whatsapp', 
		'domaintoolswhois', 
		'wishmindr', 
		'wordpress', 
		'wykop', 
		'xing', 
		'yahoomail', 
		'yammer', 
		'yookos', 
		'yoolink', 
		'yorumcuyum', 
		'youmob', 
		'yummly', 
		'yuuby', 
		'zakladoknet', 
		'ziczac', 
		'zingme'
	));
}
endif;

/* ==========================================================================
	Output AddThis Sharing Button
============================================================================= */

if( ! function_exists( 'helium_sharing_button' ) ):

function helium_sharing_button( $button ) {

	$config = array(
		'facebook_like' => array(
			'fb:like:layout' => 'button_count'
		), 
		'tweet' => array(
			'tw:count' => 'none'
		), 
		'google_plusone' => array(
			'g:plusone:size' => 'medium', 
			'g:plusone:count' => 'false'
		)
	);

	if( in_array( $button, helium_addthis_services() ) ) {

		if( 'compact' == $button ) {
			$button = 'expanded';
		}

		echo '<a class="addthis_button_' . esc_attr( $button ) . '"';

		if( array_key_exists( $button, $config ) && is_array( $config[ $button ] ) ) {
			foreach( $config[ $button ] as $key => $value ) {
				echo ' ' . $key . '="' . esc_attr( $value ) . '"';
			}
		}

		echo '></a>';
	}
}
endif;
