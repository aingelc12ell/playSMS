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

if (!auth_isadmin()) {
	auth_block();
}

include $core_config['apps_path']['plug'] . "/gateway/smstools/config.php";

switch (_OP_) {
	case "manage":
		$tpl = [
			'name' => 'smstools',
			'vars' => [
				'DIALOG_DISPLAY' => _dialog(),
				'Manage' => _('Manage'),
				'Gateway' => _('Gateway'),
				'Queue directory' => _('Queue directory'),
				'Module timezone' => _('Module timezone'),
				'Save' => _('Save'),
				'HINT_TIMEZONE' => _hint(_('Eg: +0700 for UTC+7 or Jakarta/Bangkok timezone')),
				'BUTTON_BACK' => _back('index.php?app=main&inc=core_gateway&op=gateway_list'),
				'gateway_name' => $plugin_config['smstools']['name'],
				'queue' => $plugin_config['smstools']['queue'],
				'datetime_timezone' => $plugin_config['smstools']['datetime_timezone']
			]
		];
		_p(tpl_apply($tpl));
		break;

	case "manage_save":
		$queue = isset($_REQUEST['queue']) && $_REQUEST['queue']
			? core_sanitize_path($_REQUEST['queue'])
			: '/var/spool/sms';
		$datetime_timezone = $_REQUEST['datetime_timezone'] ?? '';
		$items = [
			'queue' => $queue,
			'datetime_timezone' => $datetime_timezone
		];
		if (registry_update(0, 'gateway', 'smstools', $items)) {
			$_SESSION['dialog']['info'][] = _('Gateway module configurations has been saved');
		} else {
			$_SESSION['dialog']['danger'][] = _('Fail to save gateway module configurations');
		}
		header("Location: " . _u('index.php?app=main&inc=gateway_smstools&op=manage'));
		exit();
}
