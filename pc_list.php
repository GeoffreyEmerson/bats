<?php

	include("functions.php");
	include("jscript.html");
	html_header('HI Asset DB - Computer List','new_value');
	include("top_menu.php");

	print "<h1>HI Asset DB - Computer List</h1>\n\n";

// Get passed variables

	$pass_array = array(); // Pre-define the array to avoid cluttering the error log.

	if ( isset($_GET["search"]) AND isset($_GET["key"]) ) {
		$query_field = $_GET["search"];
		$query_key = $_GET["key"];
		$search_string = "AND lower($query_field) = lower('$query_key')";
		$pass_array['search']=$query_field;
		$pass_array['key']=$query_key;
	}

	$search_string = "AND lower(type)='pc'"; // Hack for PC page.

	// Sort variables are passed in comma separated lists. They need to be processed to make sense to the query.
	if ( isset($_GET["sort"]) ) {$sort_by = $_GET["sort"];} else {$sort_by = "computernumber";}
	if ( isset($_GET["order"]) ) {$order = $_GET["order"];} else {$order = "desc";}

	$sorts_array = explode(",",$sort_by);
	$order_array = explode(",",$order);

	// Combine the separate arrays into one
	for($i=0; isset($sorts_array[$i]) && $i<3 ; $i++) {
		if ($sorts_array[$i]=="firstname") {
			if ($order_array[$i]=="asc") {
				$arranged[$i] = "firstname asc, lastname asc";
			} else {
				$arranged[$i] = "firstname desc, lastname desc";
			}
		} else {
			$arranged[$i] = $sorts_array[$i]." ".$order_array[$i];
		}
	}

	// Now make the array into a string that can be inserted into the query
	$sort_string = implode(",",$arranged);

// Connect to database

	connect_to_db();

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
			WHERE a.active='t' $search_string
			ORDER BY $sort_string";

//			ORDER BY $sort_by $order";

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

//	column_head(current page, variable array, $sort_by variable, column to display, $order variable, default sort order);

	column_head('pc_list', $pass_array, $sort_by, 'thq', $order, 'desc');
	column_head('pc_list', $pass_array, $sort_by, 'type', $order, 'asc');
	column_head('pc_list', $pass_array, $sort_by, 'description', $order, 'asc');
	column_head('pc_list', $pass_array, $sort_by, 'serial', $order, 'asc');
	column_head('pc_list', $pass_array, $sort_by, 'purchasedate', $order, 'asc');
	column_head('pc_list', $pass_array, $sort_by, 'firstname', $order, 'asc');
	column_head('pc_list', $pass_array, $sort_by, 'department', $order, 'asc');
	column_head('pc_list', $pass_array, $sort_by, 'computernumber', $order, 'desc');
	column_head('pc_list', $pass_array, $sort_by, 'videocard', $order, 'desc');
	column_head('pc_list', $pass_array, $sort_by, 'videobus', $order, 'desc');
	column_head('pc_list', $pass_array, $sort_by, 'cpu', $order, 'desc');
	column_head('pc_list', $pass_array, $sort_by, 'harddrive', $order, 'desc');
	column_head('pc_list', $pass_array, $sort_by, 'memory', $order, 'desc');
	column_head('pc_list', $pass_array, $sort_by, 'batch', $order, 'desc');
	column_head('pc_list', $pass_array, $sort_by, 'expresscode', $order, 'desc');
	column_head('pc_list', $pass_array, $sort_by, 'warranty', $order, 'desc');
	column_head('pc_list', $pass_array, $sort_by, 'mayadongle', $order, 'desc');

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
		printf($cell,$pc['type']);
		printf($cell,$pc['description']);
		printf($cell,"<a href=\"http://support.dell.com/support/topics/global.aspx/support/my_systems_info/details?c=us&l=en&s=gen&~tab=2&servicetag=".$pc['serial']."\" target=top>".$pc['serial']."</a>");
		if ($pc['purchasedate']) {
			printf($cell,date('m-d-Y',strtotime($pc['purchasedate']) ) );
		} else {
			printf($cell,$pc['purchasedate']);
		}

		if ( ($pc['firstname']=="") || ($pc['firstname'] == "IT") || ($pc['status'] == "Proxy") || ($pc['status'] == "Build") ) {
			printf($cell,$pc['firstname']." ".$pc['lastname'] . " (".$pc['status'].")");
		} else {
			printf($cell,$pc['firstname']." ".$pc['lastname']);
		}
		printf($cell,$pc['department']);
		printf($cell,$pc['computernumber']);
		printf($cell,$pc['videocard']);
		printf($cell,$pc['videobus']);
		printf($cell,$pc['cpu']);
		printf($cell,$pc['harddrive']);
		printf($cell,$pc['memory']);
		printf($cell,$pc['batch']);
		printf($cell,$pc['expresscode']);
		printf($cell,$pc['warranty']);
		printf($cell,$pc['mayadongle']);
		print "</tr>\n";
	}

// Close table

	print "</table>\n<p>\n";



// Bottom of page

	print "End of page.\n\n";

	print "</body></html>";
?>
