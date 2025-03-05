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

$view = $_REQUEST['view'] ? $_REQUEST['view'] : 'admin';

switch (_OP_) {
	case "user_list":
		if ($view == 'admin') {
			$conditions = [
				'flag_deleted' => 0,
				'status' => 2
			];
			$form_sub_title = "<h3>" . _('List of administrators') . "</h3>";
			$disabled_on_admin = 'disabled';
		} else if ($view == 'users') {
			$conditions = [
				'flag_deleted' => 0,
				'status' => 3
			];
			$form_sub_title = "<h3>" . _('List of users') . "</h3>";
			$disabled_on_users = 'disabled';
		} else if ($view == 'subusers') {
			$conditions = [
				'flag_deleted' => 0,
				'status' => 4
			];
			$form_sub_title = "<h3>" . _('List of subusers') . "</h3>";
			$disabled_on_subusers = 'disabled';
			$parent_column_title = "<th width='12%'>" . _('Parent') . "</th>";
		}

		$search_var = [
			_('Registered') => 'register_datetime',
			_('Username') => 'username',
			_('Name') => 'name',
			_('Mobile') => 'mobile',
			_('ACL') => 'acl_id'
		];
		if ($view == 'subusers') {
			$search_var[_('Parent account')] = 'parent_uid';
		}

		$search = themes_search(
			$search_var,
			'',
			[
				'parent_uid' => 'user_username2uid',
				'acl_id' => 'acl_getid'
			]
		);
		$keywords = $search['dba_keywords'];
		$count = dba_count(_DB_PREF_ . '_tblUser', $conditions, $keywords);
		$nav = themes_nav($count, "index.php?app=main&inc=core_user&route=user_mgmnt&op=user_list&view=" . $view);
		$extras = [
			'ORDER BY' => 'register_datetime DESC, username',
			'LIMIT' => (int) $nav['limit'],
			'OFFSET' => (int) $nav['offset']
		];
		$list = dba_search(_DB_PREF_ . '_tblUser', '*', $conditions, $keywords, $extras);
		$content = _dialog();
		$content .= "
			<h2>" . _('Manage account') . "</h2>
			<input type='button' " . $disabled_on_admin . " value='" . _('Administrators') . "' onClick=\"javascript:linkto('" . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_list&view=admin') . "')\" class=\"button\" />
			<input type='button' " . $disabled_on_users . " value='" . _('Users') . "' onClick=\"javascript:linkto('" . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_list&view=users') . "')\" class=\"button\" />
			<input type='button' " . $disabled_on_subusers . " value='" . _('Subusers') . "' onClick=\"javascript:linkto('" . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_list&view=subusers') . "')\" class=\"button\" />
			" . $form_sub_title . "
			<p>" . $search['form'] . "</p>
			<div class=actions_box>
				<div class=pull-left>
					<a href=\"" . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_add&view=' . $view) . "\">" . $icon_config['add'] . "</a>
				</div>
				<div class=pull-right>
				</div>
			</div>
			<div class=table-responsive>
			<table class=playsms-table-list>
			<thead><tr>
				<th width='12%'>" . _('Registered') . "</th>
				<th width='12%'>" . _('Expired') . "</th>
				" . $parent_column_title . "
				<th width='10%'>" . _('Username') . "</th>
				<th width='12%'>" . _('Name') . "</th>
				<th width='12%'>" . _('Mobile') . "</th>
				<th width='10%'>" . _('Credit') . "</th>
				<th width='10%'>" . _('ACL') . "</th>									
				<th width='10%'>" . _('Action') . "</th>
			</tr></thead>
			<tbody>";
		$j = $nav['top'];
		$list = _display($list);
		$c_count = count($list);
		for ($i = 0; $i < $c_count; $i++) {

			$action = "";

			// login as
			if ($list[$i]['uid'] != $user_config['uid']) {
				$action .= "<a href=\"" . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=login_as&uname=' . $list[$i]['username']) . "\">" . $icon_config['login_as'] . "</a>";
			}

			// user preferences
			$action .= "<a href=\"" . _u('index.php?app=main&inc=core_user&route=user_pref&op=user_pref&uname=' . $list[$i]['username']) . "&view=" . $view . "\">" . $icon_config['user_pref'] . "</a>";

			// user configurations
			$action .= "<a href=\"" . _u('index.php?app=main&inc=core_user&route=user_config&op=user_config&uname=' . $list[$i]['username']) . "&view=" . $view . "\">" . $icon_config['user_config'] . "</a>";

			if ($list[$i]['uid'] != '1' || $list[$i]['uid'] != $user_config['uid']) {
				if (user_banned_get($list[$i]['uid'])) {
					// unban
					$action .= "<a href=\"javascript: ConfirmURL('" . addslashes(_("Are you sure you want to unban account")) . " " . $list[$i]['username'] . " ?','" . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_unban&uname=' . $list[$i]['username']) . "&view=" . $view . "')\">" . $icon_config['unban'] . "</a>";
					$banned_icon = $icon_config['ban'];
				} else {
					// ban
					$action .= "<a href=\"javascript: ConfirmURL('" . addslashes(_("Are you sure you want to ban account")) . " " . $list[$i]['username'] . " ?','" . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_ban&uname=' . $list[$i]['username']) . "&view=" . $view . "')\">" . $icon_config['ban'] . "</a>";
					$banned_icon = '';
				}
			}

			// remove user except those who still have subusers
			$subusers = user_getsubuserbyuid($list[$i]['uid']);
			if (count($subusers) > 0) {
				$action .= _hint(_('Please remove all subusers from this user to delete'));
			} else {
				$action .= "<a href=\"javascript: ConfirmURL('" . addslashes(_("Are you sure you want to delete user")) . " " . $list[$i]['username'] . " ?','" . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_del&uname=' . $list[$i]['username']) . "&view=" . $view . "')\">" . $icon_config['user_delete'] . "</a>";
			}

			// subuser shows parent column
			if ($list[$i]['status'] == 4) {
				$parent_user_data = user_getfieldbyuid($list[$i]['parent_uid'], 'status');
				$isadmin = $parent_user_data['status'] == 2 ? $icon_config['admin'] : '';
				$parent_column_row = "<td>" . user_uid2username($list[$i]['parent_uid']) . " " . $isadmin . "</td>";
			}

			$j--;
			$content .= "
				<tr>
					<td>" . core_display_datetime($list[$i]['register_datetime']) . "</td>
					<td>" . core_display_datetime($list[$i]['expired']) . "</td>
					" . $parent_column_row . "
					<td>" . $banned_icon . "" . $list[$i]['username'] . " </td>
					<td>" . $list[$i]['name'] . "</td>
					<td>" . $list[$i]['mobile'] . "</td>
					<td>" . core_display_credit(rate_getusercredit($list[$i]['username'])) . "</td>
					<td>" . acl_getnamebyuid($list[$i]['uid']) . "</td>
					<td>" . $action . "</td>
				</tr>";
		}
		$content .= "
			</tbody></table>
			</div>
			<div class=pull-right>" . $nav['form'] . "</div>";
		_p($content);
		break;

	case "user_add":
		$datetime_timezone = $_REQUEST['add_datetime_timezone'];
		$datetime_timezone = $datetime_timezone ? $datetime_timezone : core_get_timezone();

		// get language options
		$lang_list = [];
		$c_count = is_array($core_config['plugins']['list']['language']) && isset($core_config['plugins']['list']['language'])
			? count($core_config['plugins']['list']['language'])
			: 0;
		for ($i = 0; $i < $c_count; $i++) {
			$language = $core_config['plugins']['list']['language'][$i];
			$c_language_title = $plugin_config[$language]['title'];
			if ($c_language_title) {
				$lang_list[$c_language_title] = $language;
			}
		}
		$option_language_module .= "<option value=\"\">" . _('Default') . "</option>";
		if (is_array($lang_list)) {
			foreach ( $lang_list as $key => $val ) {
				if ($val == $user_config['language_module'])
					$selected = "selected";
				$option_language_module .= "<option value=\"" . $val . "\" $selected>" . $key . "</option>";
				$selected = "";
			}
		}

		// get list of users as parents
		$default_parent_uid = ($parent_uid && ($parent['uid'] == $user_edited['parent_uid']) ? $parent['uid'] : $core_config['main']['default_parent']);
		$select_parent = themes_select_account_level_single(3, 'add_parent_uid', $default_parent_uid);

		if ($view == 'admin') {
			$selected_admin = 'selected';
		} else if ($view == 'users') {
			$selected_users = 'selected';
		} else if ($view == 'subusers') {
			$selected_subusers = 'selected';
		}

		$select_status = "
			<select name='add_status'>
				<option value='2' " . $selected_admin . ">" . _('Administrator') . "</option>
				<option value='3' " . $selected_users . ">" . _('User') . "</option>
				<option value='4' " . $selected_subusers . ">" . _('Subuser') . "</option>
			</select>
		";

		// get access control list
		$option_acl = _select('add_acl_id', array_flip(acl_getall()));

		$form_title = _('Manage account');
		$form_sub_title = _('Add account');
		$button_back = _back('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_list&view=' . $view);

		$tpl = [
			'name' => 'user_add',
			'vars' => [
				'Account status' => _('Account status'),
				'Account expiry date' => _('Account expiry date'),
				'Access Control List' => _('Access Control List'),
				'Parent account' => _('Parent account') . " (" . _('for subuser only') . ")",
				'Username' => _mandatory(_('Username')),
				'Password' => _mandatory(_('Password')),
				'Name' => _mandatory(_('Name')),
				'Email' => _mandatory(_('Email')),
				'Mobile' => _mandatory(_('Mobile')),
				'Active language' => _('Active language'),
				'Timezone' => _('Timezone'),
				'Save' => _('Save'),
				'HINT_STATUS' => _hint(_('Cannot change status when user have subusers')),
				'HINT_PARENT' => _hint(_('Parent account is mandatory for subusers only. If no value is given then the subuser will be automatically assigned to user admin')),
				'STATUS' => $status_label,
				'DIALOG_DISPLAY' => _dialog(),
				'HTTP_PATH_THEMES' => _HTTP_PATH_THEMES_,
				'FORM_TITLE' => $form_title,
				'FORM_SUB_TITLE' => $form_sub_title,
				'BUTTON_BACK' => $button_back,
				'VIEW' => $view,
				'select_status' => $select_status,
				'select_parent' => $select_parent,
				'option_acl' => $option_acl,
				'option_language_module' => $option_language_module,
			],
			'ifs' => [
				'calendar' => file_exists(_HTTP_PATH_THEMES_ . '/common/jscss/bootstrap-datetimepicker/bootstrap-datetimepicker.' . substr($user_config['language_module'], 0, 2) . '.js')
			],
		];
		_p(tpl_apply($tpl));
		break;

	case "user_add_yes":
		$add['expired'] = $_POST['add_expired'];
		$add['email'] = $_POST['add_email'];
		$add['status'] = $_POST['add_status'];
		$add['acl_id'] = (int) $_POST['add_acl_id'];
		$add['username'] = $_POST['add_username'];
		$add['password'] = $_POST['add_password'];
		$add['mobile'] = $_POST['add_mobile'];
		$add['name'] = $_POST['add_name'];
		$add['footer'] = $_POST['add_footer'];
		$add['datetime_timezone'] = $_POST['add_datetime_timezone'];
		$add['language_module'] = $_POST['add_language_module'];

		// subuser's parent uid, by default its uid=1
		if ($_POST['add_parent_uid']) {
			$add['parent_uid'] = (int) ($add['status'] == 4 ? $_POST['add_parent_uid'] : $core_config['main']['default_parent']);
		} else {
			$add['parent_uid'] = (int) $core_config['main']['default_parent'];
		}
		// set credit to 0 by default
		$add['credit'] = 0;

		// add user
		$ret = user_add($add);

		if (is_array($ret)) {
			$_SESSION['dialog']['info'][] = $ret['error_string'];
		} else {
			$_SESSION['dialog']['info'][] = _('Unable to process user addition');
		}

		header("Location: " . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_add&view=' . $view));
		exit();

	case "user_del":
		$up['username'] = $_REQUEST['uname'];
		$del_uid = user_username2uid($up['username']);

		// users cannot be removed if they still have subusers
		$subusers = user_getsubuserbyuid($del_uid);
		if (count($subusers) > 0) {
			$ret['error_string'] = _('Unable to delete this user until all subusers under this user have been removed');
		} else {
			$ret = user_remove($del_uid);
		}

		$_SESSION['dialog']['info'][] = $ret['error_string'];
		header("Location: " . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_list&view=' . $view));
		exit();

	case "user_unban":
		$uid = user_username2uid($_REQUEST['uname']);
		if (user_banned_get($uid)) {
			if (user_banned_remove($uid)) {
				$_SESSION['dialog']['info'][] = _('Account has been unbanned') . ' (' . _('username') . ': ' . $_REQUEST['uname'] . ')';
			} else {
				$_SESSION['dialog']['info'][] = _('Unable to unban account') . ' (' . _('username') . ': ' . $_REQUEST['uname'] . ')';
			}
		} else {
			$_SESSION['dialog']['info'][] = _('User is not on banned users list') . ' (' . _('username') . ': ' . $_REQUEST['uname'] . ')';
		}
		header("Location: " . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_list&view=' . $view));
		exit();

	case "user_ban":
		$uid = user_username2uid($_REQUEST['uname']);
		if ($uid && ($uid == 1 || $uid == $user_config['uid'])) {
			$_SESSION['dialog']['info'][] = _('Account admin or currently logged in administrator cannot be banned');
		} else if (user_banned_get($uid)) {
			$_SESSION['dialog']['info'][] = _('User is already on banned users list') . ' (' . _('username') . ': ' . $_REQUEST['uname'] . ')';
		} else {
			if (user_banned_add($uid)) {
				$_SESSION['dialog']['info'][] = _('Account has been banned') . ' (' . _('username') . ': ' . $_REQUEST['uname'] . ')';
			} else {
				$_SESSION['dialog']['info'][] = _('Unable to ban account') . ' (' . _('username') . ': ' . $_REQUEST['uname'] . ')';
			}
		}
		header("Location: " . _u('index.php?app=main&inc=core_user&route=user_mgmnt&op=user_list&view=' . $view));
		exit();

	case "login_as":
		user_session_remove($_SESSION['uid'], $_SESSION['sid']);
		$uid = user_username2uid($_REQUEST['uname']);
		auth_login_as($uid);
		if (auth_isvalid()) {
			_log("login as u:" . $_SESSION['username'] . " uid:" . $uid . " status:" . $_SESSION['status'] . " sid:" . $_SESSION['sid'] . " ip:" . $_SERVER['REMOTE_ADDR'], 2, "user_mgmnt");
		} else {
			_log("fail to login as u:" . $_SESSION['username'] . " uid:" . $uid . " status:" . $_SESSION['status'] . " sid:" . $_SESSION['sid'] . " ip:" . $_SERVER['REMOTE_ADDR'], 2, "user_mgmnt");
		}
		header('Location: ' . _u(_HTTP_PATH_BASE_));
		exit();
}
