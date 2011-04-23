<?php
/**
 * Compatibility functions for older version of PHP and/or WordPress.
 * @package now-reading
 */

if ( !class_exists('WP_Error') ) {
    class WP_Error {
        var $errors = array();

        function WP_Error($code = '', $message = '') {
            if ( ! empty($code) )
                $this->errors[$code][] = $message;
        }

        function get_error_codes() {
            if ( empty($this->errors) )
                return array();

            return array_keys($this->errors);
        }

        function get_error_code() {
            $codes = $this->get_error_codes();

            if ( empty($codes) )
                return '';

            return $codes[0];
        }

        function get_error_messages($code = '') {
        // Return all messages if no code specified.
            if ( empty($code) ) {
                $all_messages = array();
                foreach ( (array) $this->errors as $code => $messages )
                    $all_messages = array_merge($all_messages, $messages);

                return $all_messages;
            }

            if ( isset($this->errors[$code]) )
                return $this->errors[$code];
            else
                return array();
        }

        function get_error_message($code = '') {
            if ( empty($code) )
                $code = $this->get_error_code();
            $messages = $this->get_error_messages($code);
            if ( empty($messages) )
                return '';
            return $messages[0];
        }

        function add($code, $message) {
            $this->errors[$code][] = $message;
        }
    }

    function is_wp_error($thing) {
        return ( is_object($thing) && is_a($thing, 'WP_Error') );
    }
}

if ( !function_exists('wp_get_referer') ) {
    function wp_get_referer() {
        return '';
    }
}

if ( !function_exists('nr_reset_vars') ) {
    function nr_reset_vars($vars) {
        for ($i=0; $i<count($vars); $i += 1) {
            $var = $vars[$i];
            global $$var;

            if (!isset($$var)) {
                if (empty($_POST["$var"])) {
                    if (empty($_GET["$var"]))
                        $$var = '';
                    else
                        $$var = $_GET["$var"];
                } else {
                    $$var = $_POST["$var"];
                }
            }
        }
    }
}

?>
