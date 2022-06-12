<?php

defined('BASEPATH') or exit('No direct script access allowed');
header('Content-Type: text/html; charset=utf-8');

/**
 * Is user logged in
 * @return boolean
 */
function is_user_logged_in()
{
	$CI =& get_instance();
	$userdata = $CI->session->userdata();
	if (isset($userdata) && isset($userdata['logged_in']) && $userdata['logged_in'] == true) {
		return true;
	}
	
	return false;
}
function is_customer_logged_in()
{
	$CI =& get_instance();
	$userdata = $CI->session->userdata();
	if (isset($userdata) && isset($userdata['logged_in']) && $userdata['logged_in'] == true) {
		return true;
	}
	
	return false;
}

/**
 * Is is granted
 * @return boolean
 */
function is_granted($role)
{
	$CI =& get_instance();
	$userdata = $CI->session->userdata();
	if (isset($userdata) && isset($userdata['role']) && $userdata['role'] == $role) {
		return true;
	}
	
	return false;
}

function pwd_hash($password = '', $algo = 'md5', $repeat = 1)
{
	$algos = ['md5' => 'e86cebe1', 'sha1' => '3332102a', 'sha256' => '5cc814f7', 'sha384' => '6aa61a1', 'sha512' => '3a86036f'];
	
	// more algos: 'md2', 'md4', 'md5', 'sha1', 'sha256', 'sha384', 'sha512', 'ripemd128', 'ripemd160', 'ripemd256', 'ripemd320', 'whirlpool', 'tiger128,3', 'tiger160,3', 'tiger192,3', 'tiger128,4', 'tiger160,4', 'tiger192,4', 'snefru', 'gost', 'adler32', 'crc32', 'crc32b', 'haval128,3', 'haval160,3', 'haval192,3', 'haval224,3', 'haval256,3', 'haval128,4', 'haval160,4', 'haval192,4', 'haval224,4', 'haval256,4', 'haval128,5', 'haval160,5', 'haval192,5', 'haval224,5', 'haval256,5'
	if (in_array($algo, array_keys($algos))) {
		for ($i = 0; $i < $repeat; $i++) {
			$password = hash($algo, $password);
		}
	}
	
	return $password . '|' . $repeat . '|' . $algos[$algo];
}

function pwd_verify($password = '', $password_hashed = '')
{
	$algos = ['md5' => 'e86cebe1', 'sha1' => '3332102a', 'sha256' => '5cc814f7', 'sha384' => '6aa61a1', 'sha512' => '3a86036f'];
	$items = explode("|", $password_hashed);
	
	return ($password_hashed == pwd_hash($password, array_search($items[2], $algos), $items[1]));
}
