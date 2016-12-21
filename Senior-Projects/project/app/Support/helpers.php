<?php
/**
 * Created by PhpStorm.
 * User: Siebe
 * Date: 13/12/2016
 * Time: 23:55
 */

/**
 * Function to translate a boolean to something human
 *
 * @param bool $bool
 *
 * @return string
 */
function boolh( bool $bool ) {
	return $bool ? "Yes" : "No";
}

function multidimensionalArraySearch($needle, $haystack, $key){
	return array_search($needle, array_column($haystack, $key));
}

/**
 * Echo the text with rule endings
 *
 * @param $text
 */
function echoln( $text ) {
	echo '<br>';
	echo $text;
	echo '<br>';
}