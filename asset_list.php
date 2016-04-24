<?php	// Page timer
	$stimer = explode( ' ', microtime() );
	$stimer = $stimer[1] + $stimer[0];

	// Script start
	include("functions.php");
	include("jscript.html");
	html_header('HI Asset DB - Asset List','new_value');
	include("top_menu.php");

	$db = pg_connect("host=$_ENV["HOST"] port=$_ENV["PORT"] dbname=$_ENV["DBNAME"] user=$_ENV["USER"] password=$_ENV["USER_PASS"]")
		or die('Could not connect: ' . pg_last_error());

	// Check for passed sort variables
	if ( isset($_GET["active"]) ) { $active = $_GET["active"]; } else { $active = 't'; }
	if ( isset($_GET["sort"]) ) { $sort_by = $_GET["sort"]; } else  { $sort_by = "assetid"; }
	if ( isset($_GET["order"]) ) { $order = $_GET["order"]; } else { $order = "desc"; }
	if ( isset($_GET["search"]) AND isset($_GET["key"]) ) {
		$query_field = $_GET["search"];
		$query_key = $_GET["key"];
		$search_string = "AND lower($query_field) = lower('$query_key')";
		$pass_array['search']=$query_field;
		$pass_array['key']=$query_key;
	} else {
		$search_string = "";
	}

	$query = "	SELECT * FROM asset_tbl a
			LEFT JOIN (
				SELECT DISTINCT ON (assetid_fkey) * FROM assignment_tbl
				LEFT JOIN user_tbl ON (userid_fkey=userid)
				ORDER BY assetid_fkey, datetime desc
			) AS usernames
			ON assetid=assetid_fkey
			WHERE (a.active='$active' OR a.active isnull) $search_string
			ORDER BY lower($sort_by) $order";

	$pass_array['query']=$query;

	$result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }

// Create data table

	print "<h1>HI Asset DB - Asset List</h1>\n";
//	print "<h4>Debug info: $cat - $sort_by - $order</h4>\n\n";
//	print "<h4>Query: ".$pass_array['query']."</h4>\n";
//	print "<h4>Search: ".$pass_array['search']."</h4>\n";
//	print "<h4>Key: ".$pass_array['key']."</h4>\n";

	$cell = '<td NOWRAP> %s </td>';

	print "<table>\n";
	print "<tr>";

	// Display column names with sorting options
	column_head('asset_list', $pass_array, $sort_by, 'thq', $order, 'desc');
	column_head('asset_list', $pass_array, $sort_by, 'type', $order, 'asc');
	column_head('asset_list', $pass_array, $sort_by, 'description', $order, 'asc');
	column_head('asset_list', $pass_array, $sort_by, 'serial', $order, 'asc');
	column_head('asset_list', $pass_array, $sort_by, 'purchasedate', $order, 'asc');
	column_head('asset_list', $pass_array, $sort_by, 'firstname', $order, 'asc');
	column_head('asset_list', $pass_array, $sort_by, 'department', $order, 'asc');

	print "</tr>\n";

	$row_count = 0;

	while($asset = pg_fetch_assoc($result)) {

		$row_count = $row_count + 1;
		print "<tr>";
		print "<td NOWRAP>";
		if (!$asset['thq']) {
			print "<a href=\"tag_details.php?asset=".$asset['assetid']."\">?</td>";
		} else {
			print "<a href=\"tag_details.php?asset=".$asset['assetid']."\">".$asset['thq']."</td>";
		}
		print "<td NOWRAP><a href=\"asset_list.php?search=type&key=".htmlspecialchars($asset['type'])."\">".$asset['type']."</a></td>";
		print "<td class=left><a href=\"asset_list.php?search=description&key=".htmlspecialchars($asset['description'])."\">".$asset['description']."</a></td>";
		printf($cell,$asset['serial']);
		printf($cell,$asset['purchasedate']);
		if ( ($asset['firstname']=="") || ($asset['firstname'] == "IT") ) {
			printf($cell,$asset['status']);
		} else {
			printf($cell,"<a href=user_details.php?user=" . $asset['userid'] . ">".$asset['firstname']." ".$asset['lastname']."</a>");
		}
		printf($cell,"<a href=\"asset_list.php?search=department&key=".htmlspecialchars($asset['department'])."\">".$asset['department']."</a>");
		print "</tr>\n";

	   }

	print "</table>\n\n</body>\n</html>\n\n";

	//Display page timer
	$etimer = explode( ' ', microtime() );
	$etimer = $etimer[1] + $etimer[0];
	print "<br>\n<p style=\"margin:auto; text-align:left\">";
	print "<b>" . $row_count . " rows retrieved.</b><br>\n<br>\n";
	printf( "Page generated in <b>%f</b> seconds.", ($etimer-$stimer) );
	print "</p>\n";

	//Mini-form for saving data to CSV
	print "\n";
	print "<br>\n<form name=\"form\" method=\"post\" action=\"".$_SERVER['REQUEST_URI']."\">\n";
	print "<INPUT TYPE=hidden NAME=pass_select VALUE=\"" . $query ."\">\n";
	print "Save to: <INPUT TYPE=text NAME=\"save_to\">\n";
	print "<INPUT TYPE=submit VALUE=\"Save As CSV\"></form>";

?>
