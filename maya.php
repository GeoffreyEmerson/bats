<?php

	include("functions.php");
	include("jscript.html");
	html_header('HI Asset DB - Computer List');
	include("top_menu.php");;

	print "<h1>HI Asset DB - Computer List</h1>";

	// Get sort variables
	$cat_sel = "AND lower(type)='pc'";
	if ( !($sort_by = $_GET["sort"]) ) {$sort_by = "assetid";}
	if ( !($order = $_GET["order"]) ) {$order = "desc";}
	if ( ($query_field = $_GET["search"]) AND ($query_key = $_GET["key"]) ) {$cat_sel = "AND $query_field = '$query_key'";}

	if ($sort_by=="firstname") {
		if ($order=="asc") {
			$sort_by = "firstname asc, lastname";
		} else {
			$sort_by = "firstname desc, lastname";
		}
	}


// Connect to database

	$db = pg_connect("host=$_ENV["HOST"] port=$_ENV["PORT"] dbname=$_ENV["DBNAME"] user=$_ENV["USER"] password=$_ENV["USER_PASS"]");

// Fetch Asset data

// original temp query	$query = "SELECT * FROM asset_tbl a,pc_detail_tbl c WHERE a.assetid=c.assetid ORDER BY computernumber DESC";

	$query = "	SELECT * FROM asset_tbl a
			LEFT OUTER JOIN pc_detail_tbl ON (pc_assetid=assetid)
			LEFT JOIN (
				SELECT DISTINCT ON (assetid_fkey) * FROM assignment_tbl
				LEFT JOIN user_tbl ON (userid_fkey=userid)
				ORDER BY assetid_fkey, datetime desc
			) AS usernames
			ON assetid=assetid_fkey
			WHERE a.active='t' $cat_sel
			ORDER BY $sort_by $order";

// print $query . "<p>\n\n";

/*	$query = "SELECT * FROM asset_tbl
			LEFT OUTER JOIN pc_detail_tbl ON (pc_detail_tbl.pc_assetid=asset_tbl.assetid)
			LEFT OUTER JOIN assignment_tbl ON (asset_tbl.assetid=assignment_tbl.assetid_fkey)
			LEFT OUTER JOIN user_tbl ON (assignment_tbl.userid_fkey=user_tbl.userid)
				WHERE type='PC'
				ORDER BY computernumber DESC,datetime DESC";
 */
	$result = pg_query($query);
        if (!$result) {
            echo "Problem with query: " . $query . "<br/>";
            echo pg_last_error();
            exit();
	}

// Create data table
	$cell = '<td NOWRAP> %s </td>';
	print "<table>\n";
	print "<tr>";

	// Display column names with sorting options

//	column_head(current page, $cat variable, $sort_by variable, column to display, $order variable, default sort order);

	column_head('maya', $cat, $_GET["sort"], 'thq', $order, 'desc');
	column_head('maya', $cat, $_GET["sort"], 'description', $order, 'asc');
	column_head('maya', $cat, $_GET["sort"], 'firstname', $order, 'asc');
	column_head('maya', $cat, $_GET["sort"], 'department', $order, 'asc');
	column_head('maya', $cat, $_GET["sort"], 'computernumber', $order, 'desc');
	column_head('maya', $cat, $_GET["sort"], 'mayadongle', $order, 'desc');

	print "</tr>\n";

		// Data rows
	$last_asset = '0';
	while($pc = pg_fetch_assoc($result)){
		print "<tr>";
		print "<td NOWRAP>";
		if (!$pc['thq']) {
			print "<a href=\"tag_details.php?asset=".$pc['assetid']."\">?</td>";
		} else {
			print "<a href=\"tag_details.php?asset=".$pc['assetid']."\">".$pc['thq']."</td>";
		}
		printf($cell,$pc['description']);

		if ( ($pc['firstname']=="") || ($pc['firstname'] == "IT") || ($pc['status'] == "Proxy") || ($pc['status'] == "Build") ) {
			printf($cell,$pc['firstname']." ".$pc['lastname'] . " (".$pc['status'].")");
		} else {
			printf($cell,$pc['firstname']." ".$pc['lastname']);
		}
		printf($cell,$pc['department']);
		printf($cell,$pc['computernumber']);
		printf($cell,$pc['mayadongle']);
		print "</tr>\n";
	}

// Close table

	print "</table>\n<p>\n";



// Bottom of page

	print "End of page.\n\n";

	print "</body></html>";
?>
