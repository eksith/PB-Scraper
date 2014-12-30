<?php
/**
 * Very simple pastebin.com scraper.
 * @author eksith <reksith at gmail.com>
 */
ini_set( "display_errors", true );
ini_set('max_execution_time', 100000); 

function scrape( $url ) {
	$cookie	= 'cookie.txt';
	
	$data = '';
	echo "Scraping <em>$url</em> ...";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($ch, CURLOPT_REFERER, "");
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1 );
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20 );
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; Trident/7.0; rv:11.0) like Gecko");
	
	curl_setopt($ch, CURLOPT_COOKIESESSION, true );
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie );
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie );
	
    	$data = curl_exec($ch);
    	curl_close($ch);

	echo " Done. <br />\n";
	sleep( mt_rand( 2, 7 ) );
	
	return $data;
}


function load($url) {
	$dom = new DOMDocument();
	@$dom->loadHTML(scrape( $url ));
	
        $xpath	= new DOMXPath($dom);
	$rows	= $xpath->query("//table[@class='maintable']//tr");
	$l	= $rows->length;
	if ( $l ) {
		try {
			echo "Scraping $l pastes<br /> <ol>\n";
			parse( $rows );
			echo "</ol> Done scraping.";
		} catch (Exception $e) {
			echo "Error " . time() . "<br />\n";
			die( $e->getMessage() );
		}
	}
}

function parse( &$rows ) {
	foreach( $rows as $row ) {
		$links  = $row->getElementsByTagName('a');
		if ( $links->length == 0 ) {
			continue;
		}
		$name	= $links->item(0)->nodeValue;
		$src	= $links->item(0)->getAttribute('href');
		$type	= $links->item(1)->nodeValue;
		
		$src	= substr( $src, 1, strlen( $src ) );
		save( $name, $type, $src );
	}
}

function save( $name, $type, $src ) {
	if ( strtolower( $type ) == 'none') {
		$type = 'Text';
	}
	$dir  = 'data/' . $type;
	if ( !is_dir( $dir ) ) {
		mkdir( $dir );
	}
	$fn = $dir . '/' . $src . '.txt';
	
	if( file_exists( $fn ) ) {
		echo "<li>";
		echo $src . " exists. Skipping.";
		echo "</li>";
		flush();
		ob_flush();
		return;
	}
	
	
	echo "<li>";
	$data	= scrape( 'http://pastebin.com/raw.php?i=' . $src );
	echo "</li>";
	flush();
	ob_flush();
	if ( empty( $data ) ) {
		return;
	}
	
	$f = fopen($fn, 'wb');
	fwrite( $f, "Title: " . $name . "\r\n\r\n\r\n" . $data );
	fclose( $f );
}

function init() {
	header( 'Content-type: text/html; charset=utf-8' );
	echo "Begin " . time() . "<br />\n";
	flush();
	ob_flush();
	load('http://pastebin.com/archive');
}

init();
