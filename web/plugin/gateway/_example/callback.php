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
defined('_SECRET_') or die('REMOVE THIS LINE IF YOU KNOW WHAT YOU ARE DOING');

// set gateway name and log marker
define('_CALLBACK_GATEWAY_NAME_', 'example');
define('_CALLBACK_GATEWAY_LOG_MARKER_', _CALLBACK_GATEWAY_NAME_ . ' callback');
// -------------------- START OF CALLBACK INIT --------------------
error_reporting(0);
if (!(isset($do_not_reload_init) && $do_not_reload_init === true)) {
	if ($core_config['init']['cwd'] = getcwd()) {
		if (chdir('../../../')) {
			$core_config['init']['ignore_csrf'] = true; // ignore CSRF
			if (is_file('init.php')) { // load init && functions
				include 'init.php';
				if (isset($core_config['apps_path']['libs']) && $core_config['apps_path']['libs'] && is_file($core_config['apps_path']['libs'] . '/function.php')) {
					include $core_config['apps_path']['libs'] . '/function.php';
				}
			}
			if (!(function_exists('core_sanitize_alphanumeric') && function_exists('gateway_decide_smsc'))) { // double check
				exit();
			}
			if (!(isset($core_config['init']['cwd']) && chdir($core_config['init']['cwd']))) { // go back
				exit();
			}
		} else {
			exit();
		}
	} else {
		exit();
	}
}
$requests = $_REQUEST; // get web requests
$log = ''; // log pushed vars
if (is_array($requests)) {
	foreach ( $requests as $key => $val ) {
		$log .= $key . ':' . $val . ' ';
	}
	_log("pushed " . $log, 3, _CALLBACK_GATEWAY_LOG_MARKER_);
}
// -------------------- END OF CALLBACK INIT --------------------

// Example API server only pushed these variables for DLR:
//   id - SMS Log ID sent previously by us
//   status - Delivery Report for the SMS Log ID sent previously by us
//
// read the correct variable names and their format from your API server documentation

$status = isset($requests['status']) ? core_sanitize_alphanumeric($requests['status']) : '';
$remote_id = isset($requests['id']) ? (int) $requests['id'] : 0;

// handle DLR
if ($status && $remote_id) {
	$db_query = "SELECT uid,smslog_id FROM " . _DB_PREF_ . "_tblSMSOutgoing WHERE remote_id=? AND p_status=1 AND flag_deleted=0";
	$db_result = dba_query($db_query, [$remote_id]);
	if ($db_row = dba_fetch_array($db_result)) {
		$uid = (int) $db_row['uid'];
		$smslog_id = (int) $db_row['smslog_id'];

		// proceed when the previously sent SMS to Example provider/gateway found
		if ($uid && $smslog_id) {
			// p_status :
			// 	 0 = pending
			//   1 = sent
			//   2 = failed
			//   3 = delivered
			//
			// read the correct status values and their format from your API server documentation

			// default pending
			$p_status = 0;
			switch ($status) {
				case 'DELIVRD':
				case 'ESME_ROK':
					$p_status = 3;
					break; // delivered
				default:
					$p_status = 2;
					break; // failed
			}

			// log it
			_log("dlr uid:" . $uid . " smslog_id:" . $smslog_id . " remote_id:" . $remote_id . " status:" . $status, 2, _CALLBACK_GATEWAY_LOG_MARKER_);


			// ---------- END OF CALLBACK INIT ----------
			// set delivery report
			dlr($smslog_id, $uid, $p_status);

			// respond to the API server
			// read the correct way to respond back the API server in your API server documentation
			ob_end_clean();
			echo "ACK " . $remote_id;
			exit();
		}
	}
}

// Example API server only pushed these variables for incoming SMS (MO):
//   ts - SMS timestamp for SMS date/time
//   from - SMS sender number
//   to - SMS receiver number
//   message - Content or message of incoming SMS
//
// read the correct variable names and their format from your API server documentation

$ts = isset($requests['ts']) ? (int) $requests['ts'] : 0;
$datetime = date($ts, $datetime_format);
$sender = isset($requests['from']) ? core_sanitize_mobile($requests['from']) : '';
$receiver = isset($requests['to']) ? core_sanitize_mobile($requests['to']) : '';
$message = isset($requests['message']) ? $requests['message'] : '';

// handle incoming SMS (MO)
if ($sender && $message) {
	// log it
	_log("incoming dt:" . $datetime . " from:" . $sender . " to:" . $receiver . " message:[" . $message . "]", 2, _CALLBACK_GATEWAY_LOG_MARKER_);


	// ---------- END OF CALLBACK INIT ----------
	// save incoming SMS for further processing
	$recvlog_id = recvsms($datetime, $sender, $message, $receiver, $smsc);

	// respond to the API server
	// read the correct way to respond back the API server in API server documentation
	ob_end_clean();
	echo "ACK " . $recvlog_id;
	exit();
}