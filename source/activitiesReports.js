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
YAHOO.widget.DataTable.prototype.getColumn = function(column) {
    var oColumn = this._oColumnSet.getColumn(column);

    if(!oColumn) {
        // Validate TD element
        var elCell = column.nodeName.toLowerCase() != "th" ? this.getTdEl(column) : false;
        if(elCell) {
            oColumn = this._oColumnSet.getColumn(elCell.cellIndex);
        }
        // Validate TH element
        else {
            elCell = this.getThEl(column);
            if(elCell) {
                // Find by TH el ID
                var allColumns = this._oColumnSet.flat;
                for(var i=0, len=allColumns.length; i<len; i++) {
                    if(allColumns[i].getThEl().id === elCell.id) {
                        oColumn = allColumns[i];
                    } 
                }
            }
        }
    }
    if(!oColumn) {
        YAHOO.log("Could not get Column for column at " + column, "info", this.toString());
    }
    return oColumn;
};
function success(o) {
	var results = eval(o.responseText);
	var myConfigs = {   
			paginator : new YAHOO.widget.Paginator({   
		         rowsPerPage:50  
		    })   
		 };   
	var myColumnDefs = [   
	    {key:"type", label:SUGAR.language.get("Activities", "LBL_TYPE"),sortable:true, resizeable:true, width:150},   
	    {key:"url", label:SUGAR.language.get("Activities", "LBL_SUBJECT"),sortable:true, resizeable:true, width:350},   
	    {key:"date_start", label:SUGAR.language.get("Activities", "LBL_LIST_DATE"),sortable:true,resizeable:true, width:150},   
	    {key:"status", label:SUGAR.language.get("Activities", "LBL_STATUS"),sortable:true, resizeable:true, width:100}   
    ];   
              
    var myDataSource = new YAHOO.util.DataSource(results);   
    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;   
    myDataSource.responseSchema = {fields: ["type","url","date_start","status"]};   
  
   var myDataTable = new YAHOO.widget.DataTable("activitiesDiv", myColumnDefs, myDataSource, myConfigs);   
               
    return {   
        oDS: myDataSource,   
        oDT: myDataTable   
    };   
}
