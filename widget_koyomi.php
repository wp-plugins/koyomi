<?php
/*
Plugin Name:  Japanese Koyomi Widget
Plugin URI: http://www.vjcatkick.com/?page_id=5150
Description: Display Japanese 'Koyomi' on your sidebar with moon phase.
Version: 0.0.3
Author: V.J.Catkick
Author URI: http://www.vjcatkick.com/
*/

/*
License: GPL
Compatibility: WordPress 2.6 with Widget-plugin.

Installation:
Place the widget_single_photo folder in your /wp-content/plugins/ directory
and activate through the administration panel, and then go to the widget panel and
drag it to where you would like to have it!
*/

/*  Copyright V.J.Catkick - http://www.vjcatkick.com/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/* Changelog
* Thur Jan 01 2009 - v0.0.1
- Initial release
* Thur Jan 01 2009 - v0.0.2
- Initial release - svn
* Feb 02 2009 - v0.0.3
- support date format, added 31th moon phase
*/

if ( !function_exists('_get_moon_phase_koyomi') ) :
function _get_moon_phase_koyomi() {
	$y = _get_today_value( 'Y' );
	$m = _get_today_value( 'M' );
	$d = _get_today_value( 'D' );

	//$r = ($y - 2004)*10.88 + ($m - 7)*0.97 + ($d - 1) + 13.3;
	$r = ($y - 2004)*11 + ($m - 7) + ($d - 1) + 13.3;
	do {
		if( $r >= 30 ) $r = $r - 30;
	} while( $r >= 30 );
	if( $m == 1 || $m == 2 ) { $r = $r + 2; }

	return( $r );
} /* _get_moon_phase_koyomi() */
endif;

if ( !function_exists('_get_today_value') ) :
function _get_today_value( $op ) {
	switch( $op ) {
		case 'Y':
			return date( 'Y' );
			break;
		case 'M':
			return date( 'n' );
			break;
		case 'D':
			return date( 'j' );
			break;
		default:
			return 0;
	} /* switch */
} /* _get_today_value() */
endif;

if ( !function_exists('_table_expand_koyomi') ) :
function _table_expand_koyomi( $yy ) {
	$o2ntbl = array( array(611,2350),	array(468,3222),	array(316,7317),	array(559,3402),	array(416,3493),
	array(288,2901),	array(520,1388),	array(384,5467),	array(637,605),	array(494,2349),	array(343,6443),
	array(585,2709),	array(442,2890),	array(302,5962),	array(533,2901),	array(412,2741),	array(650,1210),
	array(507,2651),	array(369,2647),	array(611,1323),	array(468,2709),	array(329,5781),	array(559,1706),
	array(416,2773),	array(288,2741),	array(533,1206),	array(383,5294),	array(624,2647),	array(494,1319),
	array(356,3366),	array(572,3475),	array(442,1450) );

	$nyymin = 1999;
	$days = $o2ntbl[ $yy - $nyymin ][0];
	$bit  = $o2ntbl[$yy - $nyymin ][1];
	$uruu = $days % 13;
	$days = intval( $days / 13 + 0.001 );

	$otbl = array();
	$otbl[] = array( $days,1 );
	if( $uruu == 0 ) {
		$bit *= 2;
		$ommax = 12;
	}else{
		$ommax = 13;
	} /* if else */

	for( $i=1;  $i <= $ommax; $i++ ) {
		$otbl[] = array( $otbl[ $i-1 ][0]+29,  $i+1 );
		if( $bit >= 4096 ) {
			$otbl[ $i ][0]++;
		} /* if */
		$bit = ( $bit % 4096 ) * 2;
	} /* for */
	$otbl[ $ommax ][1] = 0;

	if( $ommax > 12) {
		for ( $i = $uruu + 1; $i < 13 ; $i++) {
			$otbl[ $i ][1] = $i;
		} /* for */
		$otbl[ $uruu ][1] = -$uruu;
	} else {
		$otbl[13] = array(0,0);
	} /* if else */

	return( $otbl );
} /* _table_expand_koyomi() */
endif;

if ( !function_exists('_leap_year_koyomi') ) :
function _leap_year_koyomi( $yy ) {
	$ans = 0;
	if (($yy % 4) == 0) $ans = 1;
	if (($yy % 100) == 0) $ans = 0;
	if (($yy % 400) == 0) $ans = 1;
	return $ans;
} /* _leap_year_koyomi() */
endif;

if ( !function_exists('_calc_old_koyomi_today') ) :
function _calc_old_koyomi_today() {
	$nmdays = array( 31,28,31,30,31,30,31,31,30,31,30,31 );

	$y = _get_today_value( 'Y' );
	$m = _get_today_value( 'M' );
	$d = _get_today_value( 'D' );

	$nmday[1] = 28 + _leap_year_koyomi( $y );

	$theDays = $d ;
	for( $i=1;  $i < $m ; $i++) { $theDays = $theDays + $nmdays[$i - 1]; }

	$new_otbl = _table_expand_koyomi( $y );

	$oyy = $y;
	if( $theDays < $new_otbl[0][0] ) {
		$oyy--;
		$theDays += 365 + _leap_year_koyomi( $oyy );
		$new_otbl = _table_expand_koyomi( $oyy );
	} /* if */

	$omm = 0;
	$odd = 0;
	for( $i = 12; $i >= 0 ; $i--) {
		if( $new_otbl[ $i ][1] != 0) {
			if( $new_otbl[ $i ][0] <= $theDays ) {
				$omm = $new_otbl[ $i ][1];
				$odd = $theDays - $new_otbl[ $i ][0] + 1;
				break;
			} /* if */
		} /* if */
	} /* for */
	$ret = array( 'year' => $oyy, 'month' => $omm, 'day' => $odd );

	return( $ret );
} /* _calc_old_koyomi_today() */
endif;

if ( !function_exists('_get_old_koyomi_todays_str') ) :
function _get_old_koyomi_todays_str() {
	$oldd = _calc_old_koyomi_today();

	// 0.0.3
	$options = get_option('widget_j_koyomi');
	$j_koyomi_date_format = $options['j_koyomi_date_format'];
	if( !$j_koyomi_date_format ) $j_koyomi_date_format = 'm/d/Y';
	$theTime = mktime( 0,0,0, $oldd[month], $oldd[day], $oldd[year]  );
	$targetstr = date( $j_koyomi_date_format, $theTime );
//	$targetstr =  $oldd[year] . '/' . $oldd[month] . '/' . $oldd[day];

	return $targetstr;
} /* _get_old_koyomi_todays_str() */
endif;

if ( !function_exists('_get_new_koyomi_todays_str') ) :
function _get_new_koyomi_todays_str() {
	$y = _get_today_value( 'Y' );
	$m = _get_today_value( 'M' );
	$d = _get_today_value( 'D' );

	// 0.0.3
	$options = get_option('widget_j_koyomi');
	$j_koyomi_date_format = $options['j_koyomi_date_format'];
	if( !$j_koyomi_date_format ) $j_koyomi_date_format = 'm/d/Y';
	$theTime = mktime( 0,0,0, $m, $d, $y );
	$targetstr = date( $j_koyomi_date_format, $theTime );
//	$targetstr =  $y . '/' . $m . '/' . $d;

	return $targetstr;
} /* _get_new_koyomi_todays_str() */
endif;

if ( !function_exists('_get_old_koyomi_roku_val') ) :
function _get_old_koyomi_roku_val() {
	$oldd = _calc_old_koyomi_today();
	$rVal = ( $oldd[month] + $oldd[day] + 4 ) % 6;

	return( $rVal );
} /* _get_old_koyomi_roku_val() */
endif;

function widget_j_koyomi_init() {
	if ( !function_exists('register_sidebar_widget') )
		return;

	function widget_j_koyomi( $args ) {
		extract($args);

		$options = get_option('widget_j_koyomi');
		$title = $options['j_koyomi_src_title'];

		$output = '<div id="widget_j_koyomi"><ul>';

		// section main logic from here 


	$output .= '<div id="koyomi_outer" style="text-align:center;">';
	// basedir
	$basedir = get_option('siteurl') . '/wp-content/plugins/koyomi/image/';

	$moonimgdir = $basedir . 'moon/';		// +moon01-30.gif
	$r = _get_moon_phase_koyomi();
	$rr = intval( abs( $r ) );
	if( $rr < 10 ) { $rr = '0' . $rr; }
	$moonimgstr = $moonimgdir . 'moon' . $rr . '.gif';
	$output .= '<div id="moonphase" style="padding:0px; background-color: #336699; width: 63px;text-align:center;font-size:8px;color:#DDD;float:left;" >';
	$output .= '<img src="' . $moonimgstr . '" border="0" style="margin: 4px 4px 0px 4px;border:0px solid #ddd;" /><br />';
	$output .= $r;
	$output .= '</div>';	// moonphase

	// today
	$output .= '<span class="koyomi_today" style="font-size:1.25em; font-weight:bold;" >';
	$output .= _get_new_koyomi_todays_str();
	$output .= '</span><br />';

	// old
	$output .= '<span class="koyomi_old" style="font-size:1.0em; color:#888;" >';
	$output .= _get_old_koyomi_todays_str();
	$output .= '</span><br />';

	// roku
	$rVal = _get_old_koyomi_roku_val();
	$useStr = array('先勝','友引','先負','仏滅','大安','赤口');
	$useImg = array( 'roku0','roku1','roku2','roku3','roku4','roku5' );
	$rokualt = array( 
	'It is assumed that hurrying up to everything is good. It is said, [From the good luck in the morning and 2 PM to 6 PM are the misfortunes]. ',
	'Meaning of [The friend is pulled to the unlucky affair]. The good luck and daytime are large good lucks in the misfortune and the evening in the morning. However, the funeral is abhorred. ',
	'It is assumed that calm is good for everything, and is assumed the game of skill and the urgent business to have to avoid it. It is said, [It is bad in the morning, and it is good in the afternoon]. ',
	'This day is assumed to be the day of the misfortune, and there is a custom of abhorring the present of the marriage etc.Therefore, the person who holds a wedding on　this day is a little. There is a Wedding Hall that offers cut rates at the most unlucky day, too. It is said, [The prolonged buddhist ceremony is good on the day from which what also refrains if suffering]. ',
	'Meaning of [Safety very much]. It is assumed the day of the good luck. It is assumed the good luck and the day when it succeeds, and is done the marriage especially in what on this day. ',
	'It originates on the misfortune day [Red tongue day]. Besides, it is assumed the misfortune by the good luck only until about 1 PM of 11 AM. It takes care about the origin of fire and cutlery. The thing that [Death] is associated in a word is noted. ' );
	$useImgFlag = true;

	$output .= '<br /><span class="koyomi_roku" style="font-size:2.0em; font-weight:bold;" >';
	if( $useImgFlag ) {
		$rokuimgdir= $basedir . $useImg[ $rVal ] . '.gif';
		$rokuimgstr = '<img src="' . $rokuimgdir . '" alt="' . $rokualt[ $rVal ] . '" title="' . $rokualt[ $rVal ] . '" border="0"  />';
		$output .= $rokuimgstr;
	}else{
		$output .= $useStr[ $rVal ];
	} /* if else */
	$output .= '</span>';

	$output .= '</div><div style="text-align:left;">&nbsp;</div>';	// koyomi_outer


		// These lines generate the output
		$output .= '</ul></div>';

		echo $before_widget . $before_title . $title . $after_title;
		echo $output;
		echo $after_widget;
	} /* widget_j_koyomi() */

	function widget_j_koyomi_control() {
		$options = $newoptions = get_option('widget_j_koyomi');
		if ( $_POST["j_koyomi_src_submit"] ) {
			$newoptions['j_koyomi_src_title'] = strip_tags(stripslashes($_POST["j_koyomi_src_title"]));
			$newoptions['j_koyomi_date_format'] = $_POST["j_koyomi_date_format"];
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_j_koyomi', $options);
		}

		// those are default value
		if ( !$options['j_koyomi_date_format'] ) $options['j_koyomi_date_format'] = 'm/d/Y';


		$title = htmlspecialchars($options['j_koyomi_src_title'], ENT_QUOTES);
		$j_koyomi_date_format = $options['j_koyomi_date_format'];
?>

	    <?php _e('Title:'); ?> <input style="width: 170px;" id="j_koyomi_src_title" name="j_koyomi_src_title" type="text" value="<?php echo $title; ?>" /><br />
	    <?php _e('Format:'); ?> <input style="width: 170px;" id="j_koyomi_date_format" name="j_koyomi_date_format" type="text" value="<?php echo $j_koyomi_date_format; ?>" /><br />

  	    <input type="hidden" id="j_koyomi_src_submit" name="j_koyomi_src_submit" value="1" />

<?php
	} /* widget_j_koyomi_control() */

	register_sidebar_widget('Koyomi', 'widget_j_koyomi');
	register_widget_control('Koyomi', 'widget_j_koyomi_control' );
} /* widget_j_koyomi_init() */

add_action('plugins_loaded', 'widget_j_koyomi_init');

?>