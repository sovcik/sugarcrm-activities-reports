{*
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
*}
<script type="text/javascript" src="include/javascript/sugar_grp_yui_widgets.js"></script>
<script type="text/javascript" src="include/javascript/yui/build/paginator/paginator-min.js?s=' . $sugar_version . '&c=' . $sugar_config['js_custom_version'] . '"></script>
<script type="text/javascript" src="include/javascript/yui/build/yuiloader/yuiloader-min.js?s=' . $sugar_version . '&c=' . $sugar_config['js_custom_version'] . '"></script>
<script type="text/javascript" src="include/javascript/activitiesReports.js?s={$sugar_version}&c={$js_custom_version}"></script>

<table width="100%" cellpadding="1" cellspacing="1" border="0" >
	<tr>
		<td style="padding-bottom: 2px;" colspan=6>
			<form name="EditView" id="EditView" method="post" action="index.php">
				<input type="hidden" name="module" value="Activities" />
				<input type="hidden" name="run_report" id="run_report" value="0" />
				<input type="hidden" name="export_report" id="export_report" value="0" />
				<input type="hidden" name="to_pdf" id="to_pdf" value="1" />
				<input type="hidden" name="action" id="action" value="ActivitiesReports" />
		</td>
	</tr>
	<tr>
		<td width="10%">{$MOD.LBL_SELECT_MODULE}:<span class="required">*</span></td>
		<td><select id='parent_type' name='parent_type' onChange='changeQS();clearFields(false);'>
			{foreach from=$PARENT_TYPES key="KEY" item="PARENT"}
				{if $PARENT_TYPE == $KEY}
					<option value="{$KEY}" selected>{$PARENT}</option>
				{else}
					<option value="{$KEY}">{$PARENT}</option>
				{/if}

			{/foreach}		
			</select>
		</td>
	</tr>
	<tr>
		<td>{$MOD.LBL_SELECT_RECORD}:<span class="required">*</span></td>
		<td>
		<input id="parent_name" class="sqsEnabled" type="text" autocomplete="off" value="{$object_name}" size="" tabindex="p" name="parent_name"/>
		<input id="parent_id" type="hidden" value="{$object_id}" name="parent_id"/>
		<input id="object_name" type="hidden" value="{$object_name}" name="object_name"/>
		<input type="button" onclick='open_popup(document.EditView.parent_type.value, 600, 400, "", true, false, {ldelim}"call_back_function":"set_return","form_name":"EditView","field_to_name_array":{ldelim}"id":"parent_id","name":"parent_name"{rdelim}{rdelim}, "single", true);' value="Select" class="button" accesskey="T" title="Select [Alt+T]" tabindex="p" name="btn_parent_name"/>		
		<input type="button" value='{$MOD.LBL_CLEAR}' onclick="clearFields(true);" class="button" accesskey="C" title="Clear [Alt+C]" tabindex="p" name="btn_clr_parent_name"/>
		</td>
	</tr>	
	<tr>
		<td>{$MOD.LBL_FILTER_DATE_RANGE_START}: </td>
		<td>

		<input name='date_start' id='date_start' tabindex='2' size='11' maxlength='10' type="text" value="{$DATE_START}">
		<img src="{sugar_getimagepath file='jscalendar.gif'}" alt="{$USER_DATEFORMAT}" id="date_start_trigger" align="absmiddle" onclick="parseDate(document.getElementById('date_start'), '{$CALENDAR_DATEFORMAT}');">&nbsp;</td>
		 
		</td>
	</tr>
	<tr>
		<td>{$MOD.LBL_FILTER_DATE_RANGE_FINISH}: </td>
		<td><input name="date_finish" id="date_finish" type="input" tabindex='2' size='11' maxlength='10' value='{$DATE_FINISH}' />
		<img src="{sugar_getimagepath file='jscalendar.gif'}" alt="{$USER_DATEFORMAT}" id="date_finish_trigger" align="absmiddle" onclick="parseDate(document.getElementById('date_finish'), '{$CALENDAR_DATEFORMAT}');">&nbsp;</td>
		 
		</td>
	</tr>
	<tr>
		<td colspan=2><br/><input class="button" type="button" name="button" value="{$MOD.LBL_RUN_REPORT_BUTTON_LABEL}" onclick="submitForm('run');"  />
		&nbsp;&nbsp;<input class="button" type="button" name="button" value="{$MOD.LBL_EXPORT}" onclick="submitForm('export');"  />
		&nbsp;&nbsp;<input class="button" type="button" name="button" value="{$MOD.LBL_CLEAR}" onclick="clearFields(false);"  /></td>
		

	</tr>

</form>
</table>
<br/>
<div id="activitiesDiv" width="100%"></div> 

<script type="text/javascript">
Calendar.setup ({literal}{{/literal}
	inputField : "date_start", ifFormat : '{$CALENDAR_DATEFORMAT}', showsTime : false, button : "date_start_trigger", singleClick : true, step : 1, weekNumbers:false{literal}}{/literal});
Calendar.setup ({literal}{{/literal}
	inputField : "date_finish", ifFormat : '{$CALENDAR_DATEFORMAT}', showsTime : false, button : "date_finish_trigger", singleClick : true, step : 1, weekNumbers:false{literal}}{/literal});
</script>
{$quicksearch_js}
<script type="text/javascript">
enableQS(false);

function submitForm(type) {ldelim}
	//clear_all_errors();
	if (trim(document.getElementById('parent_id').value) == '') {ldelim}
		//add_error_style('EditView', 'parent_id', requiredTxt);
		alert(requiredTxt);
		return;
	{rdelim}
	
	if (type == 'export') {ldelim}
		document.EditView.object_name.value=document.getElementById('parent_name').value;
		document.EditView.export_report.value='1';
		document.getElementById('EditView').submit();
		
	{rdelim}
	else {ldelim}
		document.EditView.object_name.value=document.getElementById('parent_name').value;
		document.EditView.export_report.value='0';
		document.EditView.run_report.value='1';
		YAHOO.util.Connect.setForm(document.getElementById("EditView"));
		openConnection = YAHOO.util.Connect.asyncRequest('POST', 'index.php', {ldelim} success: success, failure:{ldelim}{rdelim}});
		
		//document.getElementById('EditView').submit();
	{rdelim}
{rdelim}

function clearFields(skipDate) {ldelim}
	document.getElementById('object_name').value = '';
	document.getElementById('parent_name').value = ''; 
	document.getElementById('parent_id').value = ''; 
	if (!skipDate) {ldelim}
		document.getElementById('date_start').value = ''; 
		document.getElementById('date_finish').value = '';
		document.getElementById('activitiesDiv').innerHTML = ''; 
	{rdelim} 

{rdelim}

function changeQS() {ldelim}
	new_module = document.EditView.parent_type.value;
	sqs_objects['parent_name']['disable'] = false;
	document.getElementById('parent_name').readOnly = false;
	sqs_objects['parent_name']['modules'] = new Array(new_module);
    enableQS(false);
{rdelim}

function set_return(popup_reply_data) {ldelim}
	var form_name = popup_reply_data.form_name;
	var name_to_value_array = popup_reply_data.name_to_value_array;
 	document.getElementById('parent_id').value = name_to_value_array['parent_id'];
 	if (name_to_value_array['name'] == 'undefined')
	 	document.getElementById('parent_name').value = name_to_value_array['parent_id'];
	else
	 	document.getElementById('parent_name').value = name_to_value_array['parent_name'];
{rdelim}
</script>

