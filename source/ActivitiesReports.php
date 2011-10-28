<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-professional-eula.html
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2010 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
if (!isset($_REQUEST['export_report']) || $_REQUEST['export_report'] != '1') {
	function js_setup($module='Accounts') {
		global $global_json;
		$global_json = getJSONobj();
		require_once('include/QuickSearchDefaults.php');
		$qsd = new QuickSearchDefaults();
		if (isset($_REQUEST['parent_type']))
				$sqs_objects = array('parent_name' => $qsd->getQSParent($_REQUEST['parent_type']));
		else
			$sqs_objects = array('parent_name' => $qsd->getQSParent($module));

		$quicksearch_js = '<script type="text/javascript" language="javascript">sqs_objects = ' . $global_json->encode($sqs_objects) . '</script>';
		return $quicksearch_js;
	}


	global $theme,$mod_strings,$current_user,$timedate;
	if (!isset($_REQUEST['to_pdf']) || $_REQUEST['to_pdf'] != '1')
		echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_ACTIVITIES_REPORTS'], false);

	global $app_list_strings;
	$parent_types = $app_list_strings['parent_type_display'];
	$disabled_parent_types = ACLController::disabledModuleList($parent_types,false, 'list');
	foreach($disabled_parent_types as $disabled_parent_type){
		unset($parent_types[$disabled_parent_type]);
	}
	global $timedate;
	$parent_types['Users']=$app_list_strings['moduleListSingular']['Users'];
	$sugar_smarty = new Sugar_Smarty();
	$sugar_smarty->assign('MOD', $mod_strings);
	$sugar_smarty->assign('APP', $app_strings);
	$sugar_smarty->assign('PARENT_TYPES', $parent_types);
	if (isset($_REQUEST['parent_type']))
		$sugar_smarty->assign('PARENT_TYPE', $_REQUEST['parent_type']);
	else
		$sugar_smarty->assign('PARENT_TYPE', '0');

	if (isset($_REQUEST['object_name']))
		$sugar_smarty->assign('object_name', $_REQUEST['object_name']);
	else
		$sugar_smarty->assign('object_name', '');

	if (isset($_REQUEST['parent_id']))
		$sugar_smarty->assign('object_id', $_REQUEST['parent_id']);
	else
		$sugar_smarty->assign('object_id', '');

	if (isset($_REQUEST['date_start']))
		$sugar_smarty->assign('DATE_START', $_REQUEST['date_start']);
	else
		$sugar_smarty->assign('DATE_START', '');
	if (isset($_REQUEST['date_finish']))
		$sugar_smarty->assign('DATE_FINISH', $_REQUEST['date_finish']);
	else
		$sugar_smarty->assign('DATE_FINISH', '');

	$sugar_smarty->assign("CALENDAR_DATEFORMAT", $timedate->get_cal_date_format());
	$sugar_smarty->assign("DATE_FORMAT", $current_user->getPreference('datef'));
	$sugar_smarty->assign("CURRENT_USER", $current_user->id);
	$firstModule = array_keys($parent_types);
	$sugar_smarty->assign("quicksearch_js", js_setup($firstModule[0]));
}

$activities = array();

if ((isset($_REQUEST['run_report']) && $_REQUEST['run_report'] == '1') ||
	(isset($_REQUEST['export_report']) && $_REQUEST['export_report'] == '1')) {

	$focus = new SugarBean();
	$query = "";
	if(ACLController::checkAccess('Calls', 'list', true)) {
	 	$query = "select 'Calls' as ";
		if ($focus->db->dbType == 'mysql')
		 	$query .= "'call' ";
	 	else
		 	$query .= "call ";

	 	$query .=",calls.description,calls.id, calls.name,calls.date_start,calls.status from calls  ";
		if (method_exists($focus,'add_team_security_where_clause'))
			$query .= $focus->add_team_security_where_clause($query, 'calls');

		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Users') {
			$query .= " INNER JOIN calls_users on calls_users.call_id=calls.id and calls_users.deleted=0 where calls_users.user_id=".
			"'" .$_REQUEST['parent_id']."'";
		}
		else {
			$query .= " where 1=1 ";
		}
	 	if (!empty($_REQUEST['date_start'])) {
	 		$query .= " and calls.date_start >= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_start'], false)." 00:00:00'", 'date');
	 	}
	 	if (!empty($_REQUEST['date_finish'])) {
	 		$query .= " and calls.date_start <= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_finish'], false)." 23:59:59'", 'date');
	 	}
		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Users') {
			$query .="  and calls.parent_id='".$_REQUEST['parent_id']."'";
		}
		$query .= " and calls.deleted=0 ";
	}
	if(ACLController::checkAccess('Tasks', 'list', true)){
		if ($query != "")
			$query .= " union all ";
		$query .="select 'Tasks',tasks.description,tasks.id, tasks.name,tasks.date_due,tasks.status from tasks ";
		if (method_exists($focus,'add_team_security_where_clause'))
			$query .= $focus->add_team_security_where_clause($query, 'tasks') ;
		$query .=" where ";
	 	if (!empty($_REQUEST['date_start'])) {
	 		$query .= "tasks.date_due >= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_start'], false)." 00:00:00'", 'date')." and ";
	 	}
	 	if (!empty($_REQUEST['date_finish'])) {
	 		$query .= "tasks.date_due <= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_finish'], false)." 23:59:59'", 'date')." and ";
	 	}
		$query .= "tasks.deleted=0 and ";
		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Users') {
			$query .= "tasks.parent_id='".$_REQUEST['parent_id']."' ";
		}
		else if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Users') {
			$query .= "tasks.assigned_user_id='".$_REQUEST['parent_id']."' ";
		}
	}
	if(ACLController::checkAccess('Meetings', 'list', true)) {
		if ($query != "")
			$query .= " union all ";

		$query .="select 'Meetings',meetings.description,meetings.id, meetings.name,meetings.date_start,meetings.status from meetings ";
		if (method_exists($focus,'add_team_security_where_clause'))
			$query .= $focus->add_team_security_where_clause($query, 'meetings');

		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Users') {
			$query .= " INNER JOIN meetings_users on meetings_users.meeting_id=meetings.id and meetings_users.deleted=0 where meetings_users.user_id=".
			"'" .$_REQUEST['parent_id']."'";
		}
		else {
			$query .= " where 1=1 ";
		}

	 	if (!empty($_REQUEST['date_start'])) {
	 		$query .= " and meetings.date_start >= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_start'], false)." 00:00:00'", 'date');
	 	}
	 	if (!empty($_REQUEST['date_finish'])) {
	 		$query .= " and meetings.date_start <= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_finish'], false)." 23:59:59'", 'date');
	 	}
		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Users') {
			$query .="  and meetings.parent_id='".$_REQUEST['parent_id']."' ";
		}
		$query .= " and meetings.deleted=0 union all select 'Notes',notes.description,notes.id, notes.name,notes.date_entered,'None' from notes ";
		if (method_exists($focus,'add_team_security_where_clause'))
			$query .= $focus->add_team_security_where_clause($query, 'notes');
		$query .= " where notes.deleted=0 and notes.parent_id='".$_REQUEST['parent_id']."'";
	 	if (!empty($_REQUEST['date_start'])) {
	 		$query .= " and notes.date_entered >= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_start'], false)." 00:00:00'", 'date');
	 	}
	 	if (!empty($_REQUEST['date_finish'])) {
	 		$query .= " and notes.date_entered <= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_finish'], false)." 23:59:59'", 'date');
	 	}

	}
	if(ACLController::checkAccess('Emails', 'list', true)) {
		if ($query != "")
			$query .= " union all ";
		$query .="select 'Emails', '', emails.id,emails.name,emails.date_sent,emails.status from emails ";
		if (method_exists($focus,'add_team_security_where_clause'))
			$query .= $focus->add_team_security_where_clause($query, 'emails');
		$query .= "	where emails.deleted=0 and ";

	 	if (!empty($_REQUEST['date_start'])) {
	 		$query .= "emails.date_sent >= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_start'], false)." 00:00:00'", 'date')." and ";
	 	}
	 	if (!empty($_REQUEST['date_finish'])) {
	 		$query .= "emails.date_sent <= ".db_convert("'".$timedate->to_db_date($_REQUEST['date_finish'], false)." 23:59:59'", 'date')." and ";
	 	}


		if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Users') {
			$query .= "emails.parent_id='".$_REQUEST['parent_id']."' ";
		}
		else if (isset($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Users') {
			$query .= "emails.assigned_user_id='".$_REQUEST['parent_id']."' ";
		}
	}
    $result = $focus->db->query($query, true, "");
    $row = $focus->db->fetchByAssoc($result);

    while ($row != null) {
        $activity['type']= $app_list_strings['moduleListSingular'][$row['call']];
	    $activity['url'] = "<a href='index.php?module=".$activity['type']."s&action=DetailView&record=".$row['id']."'>".$row['name']."</a>";
    	if (isset($_REQUEST['export_report']) && $_REQUEST['export_report'] == '1' ) {
	    	$activity['description'] = $row['description'];
	        $activity['name'] = $row['name'];
    	}
        $activity['date_start'] = $timedate->to_display_date_time($row['date_start']);
        if ($row['status'] == 'None')
        	$activity['status'] = $mod_strings['LBL_NONE_STRING'];
        else
        	$activity['status'] = $row['status'];
        array_push($activities, $activity);
        $row = $focus->db->fetchByAssoc($result);
    }
    if (isset($_REQUEST['export_report']) && $_REQUEST['export_report'] == '1' ) {
		require_once('include/export_utils.php');
		$content = '"'.preg_replace("/\"/","\"\"",$mod_strings['LBL_TYPE']).'"'.getDelimiter().
		'"'.preg_replace("/\"/","\"\"",$mod_strings['LBL_SUBJECT']).'"'. getDelimiter().
		'"'.preg_replace("/\"/","\"\"",$mod_strings['LBL_DATE']).'"'. getDelimiter().
		'"'.preg_replace("/\"/","\"\"",$mod_strings['LBL_STATUS']).'"'. getDelimiter().
		'"'.preg_replace("/\"/","\"\"",$mod_strings['LBL_CHART_DESCRIPTION']).'"'. "\r\n";
		foreach($activities as $activity) {
			$content .= '"'.preg_replace("/\"/","\"\"",$activity['type']). '"'.getDelimiter().'"'.preg_replace("/\"/","\"\"",$activity['name']).
				'"'.getDelimiter().'"'.preg_replace("/\"/","\"\"",$timedate->to_display_date_time($activity['date_start'])).'"'.getDelimiter().'"'.preg_replace("/\"/","\"\"",$activity['status']).
				'"'.getDelimiter().'"'.preg_replace("/\"/","\"\"",$activity['description']).'"'."\r\n";
		}
	    ob_clean();
		header("Pragma: cache");
		header("Content-type: application/octet-stream; charset=".$locale->getExportCharset());
		header("Content-Disposition: attachment; filename={$_REQUEST['module']}.csv");
		header("Content-transfer-encoding: binary");
		header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
		header( "Cache-Control: post-check=0, pre-check=0", false );
		header("Content-Length: ".strlen($content));
		print $GLOBALS['locale']->translateCharset($content, 'UTF-8', $locale->getExportCharset());
		exit;

    }
    else {
    	$json = getJSONobj();
		echo $json->encode($activities);
		return;

    }
}
$sugar_smarty->assign('count',count($activities));
$sugar_smarty->assign('Activities',$activities);
echo $sugar_smarty->fetch('modules/Activities/ActivitiesReports.tpl');

?>
