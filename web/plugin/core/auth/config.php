<?php

/**
 * This file is part of playSMS.
 *
 * playSMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * playSMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with playSMS. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_SECURE_') or die('Forbidden');

// captcha options
$auth_captcha_length = 5;
$auth_captcha_seed = 'efhkmnpqrwxyz98765432';
$auth_captcha_width = 256;
$auth_captcha_height = 80;

// enable/disable captcha

$auth_captcha_form_login = getenv('ENABLE_CAPTCHA_LOGIN') ? in_array(getenv('ENABLE_CAPTCHA_LOGIN'), array('true', '1', 'yes')) : true;
$auth_captcha_form_forgot = getenv('ENABLE_CAPTCHA_FORGOT') ? in_array(getenv('ENABLE_CAPTCHA_FORGOT'), array('true', '1', 'yes')) : true;
$auth_captcha_form_register = getenv('ENABLE_CAPTCHA_REGISTER') ? in_array(getenv('ENABLE_CAPTCHA_REGISTER'), array('true', '1', 'yes')) : true;

