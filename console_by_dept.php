<html>
<title>HI Asset DB - Consoles View</title>
<head>
<link rel="stylesheet" type="text/css" href="assetdb.css" />
<?php include("jscript.html"); ?>
</head>
<body>

<?php


	include("functions.php");
	include("top_menu.php");

	$db = pg_connect("host=$_ENV["HOST"] port=$_ENV["PORT"] dbname=$_ENV["DBNAME"] user=$_ENV["USER"] password=$_ENV["USER_PASS"]")
		or die('Could not connect: ' . pg_last_error());

	// Get sort variables
	if ( !($sort_by = $_GET["sort"]) ) {$sort_by = "department";}
	if ( !($order = $_GET["order"]) ) {$order = "asc";}


	print "<h1>List of Consoles by Department</h1>\n";
	print "<p>\n";


// Wii
	print "<h3>Wii Consoles</h3>\n";

	$query = "	SELECT * FROM asset_tbl a
			LEFT JOIN (
				SELECT DISTINCT ON (assetid_fkey) * FROM assignment_tbl
				LEFT JOIN user_tbl ON (userid_fkey=userid)
				ORDER BY assetid_fkey, datetime desc
			) AS usernames
			ON assetid=assetid_fkey
			WHERE (a.active='t' OR a.active isnull) AND description LIKE 'Wii%'
			ORDER BY $sort_by $order, description asc, firstname asc, lastname asc";


	$result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }

	// Create data table

	print "<table class=noframe>\n";
	print "<tr>";

	// Column headings
	print "<td>THQ #</td>";
	print "<td>Description</td>";
	print "<td>Assigned To</td>";
	print "<td>Department</td>";

	print "</tr>\n";
	print "<tr>";
	$row_count = 0;

	while($asset = pg_fetch_assoc($result)) {

		//Sum up dept totals
		if ( ($lastrow_dept != $asset['department']) and ($row_count!=0) ) {
			print "<td>$lastrow_dept: $row_count</td></tr><tr><td></td></tr>\n";
			$row_count = 0;
		} else {
			print "</tr>\n";
		}
		print "<td NOWRAP>";
		if (!$asset['thq']) {
			print "<a href=\"tag_details.php?asset=".$asset['assetid']."\">?</td>";
		} else {
			print "<a href=\"tag_details.php?asset=".$asset['assetid']."\">".$asset['thq']."</td>";
		}
		print "<td class=left>".$asset['description']."</td>";
		printf($cell,$asset['firstname']." ".$asset['lastname']);
		printf($cell,$asset['department']);
		$lastrow_dept = $asset['department'];
		$row_count = $row_count+1;
	}
	print "<td>$lastrow_dept: $row_count</td></tr>\n";
	print "</tr>\n";
	print "</table>\n\n";

// Xbox 360

	print "<h3>Xbox 360 Consoles</h3>\n";

	$query = "	SELECT * FROM asset_tbl a
			LEFT JOIN (
				SELECT DISTINCT ON (assetid_fkey) * FROM assignment_tbl
				LEFT JOIN user_tbl ON (userid_fkey=userid)
				ORDER BY assetid_fkey, datetime desc
			) AS usernames
			ON assetid=assetid_fkey
			WHERE (a.active='t' OR a.active isnull) AND description LIKE 'Xbox 360%'
			ORDER BY $sort_by $order, department asc, description asc, firstname asc, lastname asc";


	$result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }

	// Create data table

	print "<table class=noframe>\n";
	print "<tr>";

	// Column headings
	print "<td>THQ #</td>";
	print "<td>Description</td>";
	print "<td>Assigned To</td>";
	print "<td>Department</td>";

	print "</tr>\n";
	print "<tr>";
	$row_count = 0;

	while($asset = pg_fetch_assoc($result)) {

		//Sum up dept totals
		if ( ($lastrow_dept != $asset['department']) and ($row_count!=0) ) {
			print "<td>$lastrow_dept: $row_count</td></tr><tr><td></td></tr>\n";
			$row_count = 0;
		} else {
			print "</tr>\n";
		}
		print "<td NOWRAP>";
		if (!$asset['thq']) {
			print "<a href=\"tag_details.php?asset=".$asset['assetid']."\">?</td>";
		} else {
			print "<a href=\"tag_details.php?asset=".$asset['assetid']."\">".$asset['thq']."</td>";
		}
		print "<td class=left>".$asset['description']."</td>";
		printf($cell,$asset['firstname']." ".$asset['lastname']);
		printf($cell,$asset['department']);
		$lastrow_dept = $asset['department'];
		$row_count = $row_count+1;
	}
	print "<td>$lastrow_dept: $row_count</td></tr>\n";
	print "</tr>\n";
	print "</table>\n\n";

// PS3

	print "<h3>PS3 Consoles</h3>\n";

	$query = "	SELECT * FROM asset_tbl a
			LEFT JOIN (
				SELECT DISTINCT ON (assetid_fkey) * FROM assignment_tbl
				LEFT JOIN user_tbl ON (userid_fkey=userid)
				ORDER BY assetid_fkey, datetime desc
			) AS usernames
			ON assetid=assetid_fkey
			WHERE (a.active='t' OR a.active isnull) AND description LIKE 'PS3%' AND type = 'Console'
			ORDER BY $sort_by $order, department asc, description asc, firstname asc, lastname asc";


	$result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }

	// Create data table

	print "<table class=noframe>\n";
	print "<tr>";

	// Column headings
	print "<td>THQ #</td>";
	print "<td>Description</td>";
	print "<td>Assigned To</td>";
	print "<td>Department</td>";

	print "</tr>\n";
	print "<tr>";
	$row_count = 0;

	while($asset = pg_fetch_assoc($result)) {

		//Sum up dept totals
		if ( ($lastrow_dept != $asset['department']) and ($row_count!=0) ) {
			print "<td>$lastrow_dept: $row_count</td></tr><tr><td></td></tr>\n";
			$row_count = 0;
		} else {
			print "</tr>\n";
		}
		print "<td NOWRAP>";
		if (!$asset['thq']) {
			print "<a href=\"tag_details.php?asset=".$asset['assetid']."\">?</td>";
		} else {
			print "<a href=\"tag_details.php?asset=".$asset['assetid']."\">".$asset['thq']."</td>";
		}
		print "<td class=left>".$asset['description']."</td>";
		printf($cell,$asset['firstname']." ".$asset['lastname']);
		printf($cell,$asset['department']);
		$lastrow_dept = $asset['department'];
		$row_count = $row_count+1;
	}
	print "<td>$lastrow_dept: $row_count</td></tr>\n";
	print "</tr>\n";
	print "</table>\n\n";
?>

</table>
</body>
</html>
