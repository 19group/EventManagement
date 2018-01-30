<?php
// vim: foldmethod=marker
namespace App\Pesapal\Classes;

/* Generic exception class
 */
if(!class_exists("OAuthException") ) {
	class OAuthException extends Exception {
	  // pass
	}
}