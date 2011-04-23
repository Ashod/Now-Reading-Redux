<?php

/**
 * Handles the querying of the Amazon product database
 * @package now-reading
 */

/**
 * Fetches and parses XML from Amazon for the given query.
 * @param string $query Query string containing variables to search Amazon for. Valid variables: $isbn, $title, $author
 * @return array Array containing each book's information.
 */
function query_amazon( $query ) {

	require_once dirname(__FILE__) . '/sha256.inc.php';

	if (!function_exists('hmac'))
	  {
	   function hmac($key, $data, $hashfunc='sha256') 
		{
		 $blocksize=64;
		
		 if (strlen($key) > $blocksize) $key=pack('H*', $hashfunc($key));
		 $key=str_pad($key, $blocksize, chr(0x00));
		 $ipad=str_repeat(chr(0x36), $blocksize);
		 $opad=str_repeat(chr(0x5c), $blocksize);
		 $hmac = pack('H*', $hashfunc(($key^$opad) . pack('H*', $hashfunc(($key^$ipad) . $data))));
		 return $hmac;
		}
	  }
  
    global $item, $items;

    $options = get_option('nowReadingOptions');

    $using_isbn = false;

    parse_str($query);

    if ( empty($isbn) && empty($title) && empty($author) )
        return false;

    if ( !empty($isbn) )
        $using_isbn = true;

    // Our query needs different vars depending on whether or not we're searching by ISBN, so build it here.
    if ( $using_isbn ) {
        $isbn = preg_replace('#([^0-9x]+)#i', '', $isbn);
        $query = "isbn:$isbn";
    } else {
        $query='';
        if ( !empty($title) )
            $query = 'title:' . urlencode($title);
        if ( !empty($author) )
            $query .= 'author:' . urlencode($author);
    }

    // these items MUST be set in the Options screen
    $AWSAccessKeyId = trim($options['AWSAccessKeyId']);
    $SecretAccessKey = trim($options['SecretAccessKey']);

    # // some paramters
    $method = "GET";
    $host = "ecs.amazonaws".$options['domain'];
    $uri = "/onca/xml";

    // additional parameters
    $params["Service"] = "AWSECommerceService";
    // GMT timestamp
    $params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
    // API version
    $params["Version"] = "2009-03-31";
    $params["AssociateTag"] = urlencode($options['associate']);
    $params["Power"] = $query;
    $params["Operation"] = "ItemSearch";
    $params["SearchIndex"] = "Books";
    $params["ResponseGroup"] = "Request,Large,Images";
    $params["AWSAccessKeyId"] = $AWSAccessKeyId;
	
	
	// Sort paramters
    ksort($params);
   
   // re-build the request 
   $request = array(); 
    foreach ($params as $parameter=>$value) 
     { 
      $parameter = str_replace("_", ".", $parameter); 
      $parameter = str_replace("%7E", "~", rawurlencode($parameter)); 
      $value = str_replace("%7E", "~", rawurlencode($value)); 
      $request[] = $parameter . "=" . $value; 
     } 
   $request = implode("&", $request);

   $signatureString = $method . chr(10) . $host . chr(10) . $uri . chr(10) . $request;
  
   $signature = urlencode(base64_encode(hmac($SecretAccessKey, $signatureString)));   
 
   $request = "http://" . $host . $uri . "?" . $request . "&Signature=" . $signature; 


    // Fetch the XML using either Snoopy or cURL, depending on our options.
    if ( $options['httpLib'] == 'curl' ) {
        if ( !function_exists('curl_init') ) {
            return new WP_Error('curl-not-installed', __('cURL is not installed correctly.', NRTD));
        } else {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Now Reading ' . NOW_READING_VERSION);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            if ( !empty($options['proxyHost']) ) {
                $proxy = $options['proxyHost'];

                if ( !empty($options['proxyPort']) ) {
                    $proxy .= ":{$options['proxyPort']}";
                }

                curl_setopt($ch, CURLOPT_PROXY, $proxy);
            }

            $xmlString = curl_exec($ch);

            curl_close($ch);
        }
    } else {
        require_once ABSPATH . WPINC . '/class-snoopy.php';

        $snoopy = new snoopy;
        $snoopy->agent = 'Now Reading ' . NOW_READING_VERSION;

        if ( !empty($options['proxyHost']) )
            $snoopy->proxy_host = $options['proxyHost'];
        if ( !empty($options['proxyHost']) && !empty($options['proxyPort']) )
            $snoopy->proxy_port = $options['proxyPort'];

        $snoopy->fetch($request);

        $xmlString = $snoopy->results;
    }

    if ( empty($xmlString) ) {
        do_action('nr_search_error', $query);
        echo '
		<div id="message" class="error fade">
			<p><strong>' . __("Oops!") . '</strong></p>
			<p>' . sprintf(__("For some reason, I couldn't search for your book on amazon%s.", NRTD), $options['domain']) . '</p>
			<p>' . __("Amazon's Web Services may be down, or there may be a problem with your server configuration.") . '</p>
								
					';
        if ( $options['httpLib'] )
            echo '<p>' . __("Try changing your HTTP Library setting to <strong>cURL</strong>.", NRTD) . '</p>';
        echo '
		</div>
		';
        return false;
    }

    require_once 'xml/IsterXmlSimpleXMLImpl.php';

    $impl = new IsterXmlSimpleXMLImpl;
    $xml = $impl->load_string($xmlString);

    if ( $options['debugMode'] )
        robm_dump("raw XML:", htmlentities(str_replace(">", ">\n", str_replace("<", "\n<", $xmlString))));

    $items = $xml->ItemSearchResponse->Items->children();

    if ( count($items) > 0 ) {

        $results = array();

        foreach ( $items as $item ) {
            $attr = $item->ItemAttributes;

            if ( !$attr )
                continue;

            $author = '';
            if ( is_array($attr->Author) ) {
                foreach ( $attr->Author as $a ) {
                    if (is_object($a)) {
                        $author .= $a->CDATA() . ', ';
                    }
                }
                $author	= substr($author, 0, -2);
            } else {
                if (is_object($attr->Author)) {
                    $author	= $attr->Author->CDATA();
                }
            }

            if ( empty($author) )
                $author = apply_filters('default_book_author', 'Unknown');

            $title = $attr->Title->CDATA();
            if ( empty($title) )
                continue;

            $asin = $item->ASIN->CDATA();
            if ( empty($asin) )
                continue;

            if ( $options['debugMode'] )
                robm_dump("book:", $author, $title, $asin);

            $size = "{$options['imageSize']}Image";
            if (empty($item->$size))
                continue;
            $image = $item->$size->URL->CDATA();
            if ( empty($image) )
                $image = get_option('siteurl') . '/wp-content/plugins/now-reading-reloaded/no-image.png';

            $results[] = apply_filters('raw_amazon_results', compact('author', 'title', 'image', 'asin'));
        }

        $results = apply_filters('returned_books', $results);
    } else {

        return false;

    }

    return $results;
}

?>