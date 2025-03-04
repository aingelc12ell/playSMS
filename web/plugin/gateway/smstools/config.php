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

// gateway configuration in registry
$reg = gateway_get_registry('smstools');
$reg['queue'] = isset($reg['queue']) && $reg['queue']
	? core_sanitize_path($reg['queue'])
	: '/var/spool/sms';

// plugin configuration
$plugin_config['smstools'] = [
	'name' => 'smstools',
	'queue' => $reg['queue'],
];

// smsc configuration
$plugin_config['smstools']['_smsc_config_'] = [
	'sms_receiver' => _('Receiver number'),
	'queue' => _('Queue directory')
];
