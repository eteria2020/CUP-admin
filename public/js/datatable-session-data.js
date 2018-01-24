/* global $, filters:true */

/**
 * This function initialize an object "dataTableVars" containing the DataTable variables,
 * from a given (JSON) session stored data variable "filters".
 *
 * @param filters object
 * @param tableVars object
 */
var getSessionVars = function(filters, dataTableVars) {
    if (typeof filters !== "undefined" && filters !== null){
        /**
         * Required:
         *   filters{
         *       searchValue,
         *       column,
         *       iSortCol_0,
         *       sSortDir_0,
         *       iDisplayLength
         *   }
         */
        if (typeof filters.searchValue !== "undefined"){
            dataTableVars.searchValue.val(filters.searchValue);
        }
        if (typeof filters.column !== "undefined"){
            dataTableVars.column.val(filters.column);
        }
        if (typeof filters.iSortCol_0 !== "undefined"){
            dataTableVars.iSortCol_0 = filters.iSortCol_0;
        }
        if (typeof filters.sSortDir_0 !== "undefined"){
            dataTableVars.sSortDir_0 = filters.sSortDir_0;
        }
        if (typeof filters.iDisplayLength !== "undefined"){
            dataTableVars.iDisplayLength = filters.iDisplayLength;
        }
        /**
         * Optionals:
         *   filters{
         *       from,
         *       columnFromDate,
         *       to,
         *       columnFromEnd
         *   }
         */
        if (typeof filters.from !== "undefined" && filters.from !== ""){
            //dataTableVars.from.val(filters.from);
            dataTableVars.from = filters.from;
        }
        if (typeof filters.columnFromDate !== "undefined"){
            dataTableVars.columnFromDate = filters.columnFromDate;
        }
        if (typeof filters.to !== "undefined" && filters.to !== ""){
            dataTableVars.to = filters.to;
        }
        if (typeof filters.columnFromEnd !== "undefined"){
            dataTableVars.columnFromEnd = filters.columnFromEnd;
        }
        if (typeof filters.fromDate !== "undefined" && filters.fromDate !== ""){
            dataTableVars.from.val(filters.fromDate);
        }
        if (typeof filters.toDate !== "undefined" && filters.toDate !== ""){
            dataTableVars.to.val(filters.toDate);
        }
        if (typeof filters.columnToDate !== "undefined"){
            dataTableVars.columnToDate = filters.columnToDate;
        }
    }
};
