<?php

use Moltin\SDK\Request\CURL as Request;
use Moltin\SDK\Storage\Session as Storage;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Moltin' ) ) :

/**
 * Moltin Class
 */
class Moltin extends \Moltin\SDK\SDK {

	const 	CACHE_PATH 	  = '../cache';
	const 	CACHE_TIMEOUT = 14400;

	private $_moltin;
	private $_client_key;
	private $_client_secret;

    private $_messages = array();

    public  $moltin_api_cached, $moltin_api_new = 0;

    public function __construct($args = array()) {

    	parent::__construct(new Storage(), new Request(), $args);

		$this->_client_key    = get_option('moltin_key');
		$this->_client_secret = get_option('moltin_secret');

        $this->_messages      = unserialize($_SESSION['moltin_messages']);

    }

    public function set_message($msg, $type = 'info') {
        $this->_messages[$type][] = $msg;

        return $_SESSION['moltin_messages'] = serialize($this->_messages);
    }

    public function get_messages($type = false) {
        if($type) {
            $messages = $this->_messages[$type];
        } else {
            $messages = $this->_messages;
        }

        $this->_messages = array();
        unset($_SESSION['moltin_messages']);

        return $messages;
    }

    public function authenticate() {
        return parent::authenticate(new \Moltin\SDK\Authenticate\ClientCredentials(), array(
    		'client_id'     => $this->_client_key,
    		'client_secret' => $this->_client_secret,
        ));
    }

    public function refresh($args = array()) {
        return parent::refresh($args);
    }

    public function fields($type, $id = null, $wrap = false, $suffix = 'fields') {
        return parent::fields($type, $id, $wrap, $suffix);
    }

    public function __call($method, $args) {
        $call  = $args[0];

        $bypass_cache = false;

        if(is_array($args[1]) && isset($args[1]['bypass_cache'])) {
            $bypass_cache = $args[1]['bypass_cache'];
            unset($args[1]['bypass_cache']);
        }

        $ident = @implode('|', $args[1]);

    	$cache = plugin_dir_path(__FILE__) . self::CACHE_PATH . '/' . $call . '/' . md5(AUTH_KEY . $ident) . '.xml';

        // If we are using another method, delete any caches we may have
        if($method != 'get') {
            unlink($cache);

            $split = explode('/', $call);
            
            if($split[0] == 'cart') {

                // Recache the users basket
                unlink(plugin_dir_path(__FILE__) . self::CACHE_PATH . '/' . $split[0] . '/' . $split[1] . '/' . md5(AUTH_KEY) . '.xml');
            }
        }
    	// Check for cache first
    	elseif(($method == 'get') && !$bypass_cache && $refresh_time = @filemtime($cache)) {
    		// Is it in date?
    		if($refresh_time > time() - self::CACHE_TIMEOUT) {
                ++$this->moltin_api_cached;

    			// Return cached contents
    			return unserialize(file_get_contents($cache));
    		} else {
    			// Remove file and get new
    			unlink($cache);
    		}
    	}
        
    	// If no cache found, run actual call
    	try {
    		$result = parent::__call($method, $args);
    	}
    	catch(Exception $e) {
    		$result = $e->getMessage();
    	}

        if($method == 'get' && $result['status'] === true) {
    	   // Cache the result
    		if(!file_exists(dirname($cache))) {
        		mkdir(dirname($cache), 0777, true);
    		}

    		file_put_contents($cache, serialize($result));

            // If searching for products, we should cache ALL products it returns. Saves additional calls
            if($call == 'products/search') {
                foreach($result['result'] as $p) {
                    $p_cache = plugin_dir_path(__FILE__) . self::CACHE_PATH . '/product/' . md5(AUTH_KEY . $p['slug']) . '.xml';

                    file_put_contents($p_cache, serialize(array('status' => true, 'result' => $p)));
                }
            }
            // If searching for categories, we should cache ALL products it returns. Saves additional calls
            elseif($call == 'categories') {
                foreach($result['result'] as $c) {
                    $c_cache = plugin_dir_path(__FILE__) . self::CACHE_PATH . '/category/' . md5(AUTH_KEY . $c['slug']) . '.xml';

                    file_put_contents($c_cache, serialize(array('status' => true, 'result' => $c)));
                }
            }
        }

        ++$this->moltin_api_new;

    	return $result;
    }

}

endif;