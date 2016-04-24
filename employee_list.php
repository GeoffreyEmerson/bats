<?php

	include("functions.php");
	include("jscript.html");
	html_header('HI Asset DB - Employee List','');
	include("top_menu.php");

	print "<h1>HI Asset DB - Employee List</h1>";


	// Create the Add button or entry fields

	if ( isset($_GET["add"]) ) {
		print "<FORM name=\"new_user_form\" method=\"post\" action=\"adduser.php\">\n";
		print "<table>\n";
		print "<tr><td>First Name:</td><td><INPUT TYPE=text NAME=pass_fname></td></tr>\n";
		print "<tr><td>Last Name:</td><td><INPUT TYPE=text NAME=pass_lname></td></tr>\n";
		print "<tr><td>Department:</td><td><INPUT TYPE=text NAME=pass_dept></td></tr>\n";
		print "<tr><td colspan=2 style=\"align: center\"><INPUT TYPE=submit VALUE=\"Add User\"></td></tr>\n";
		print "</table><p>\n\n";
	} else {
		print " <a href=employee_list.php?add='t'>Add User</a>";
	}


	// Start database connect and query for active users

	$db = pg_connect("host=$_ENV["HOST"] port=$_ENV["PORT"] dbname=$_ENV["DBNAME"] user=$_ENV["USER"] password=$_ENV["USER_PASS"]")
		or die('Could not connect: ' . pg_last_error());

	if ( isset($_GET["sort"]) ) {
		$sort_by = $_GET["sort"];
	} else {
		$sort_by = "firstname,lastname";
	}

	if ( isset($_GET["order"]) ) { $order = $_GET["order"]; } else { $order = ""; }

	$query = "SELECT * FROM user_tbl WHERE active='t' ORDER BY ".$sort_by." ".$order;

	$result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
	}

	print "<table>\n";
	print "<tr><td colspan=3 style=\"valign: center;\">ACTIVE USERS ";
	print "</td></tr>\n<tr>";

	// Display column names with sorting options

	if ( ($sort_by == "userid") && ($order != "desc") ) {
		print "<td><a href=\"employee_list.php?sort=userid&order=desc\">ID</a></td>"; } else {
		print "<td><a href=\"employee_list.php?sort=userid\">ID</a></td>"; }
	if ($sort_by == "lastname") {
		print "<td><a href=\"employee_list.php\">Name</a></td>";} else {
		print "<td><a href=\"employee_list.php?sort=lastname\">Name</a></td>";}
	if ( ($sort_by == "department") && ($order != "desc") ) {
		print "<td><a href=\"employee_list.php?sort=department&order=desc\">Department</a></td>"; } else {
		print "<td><a href=\"employee_list.php?sort=department\">Department</a></td>"; }

	while($myrow = pg_fetch_assoc($result)) {
		print "<tr>";
		print "<td NOWRAP><a href=\"user_details.php?user=".$myrow['userid']."\">".$myrow['userid']."</a></td>";
		print "<td NOWRAP><a href=\"user_details.php?user=".$myrow['userid']."\">".htmlspecialchars($myrow['firstname'])." ".htmlspecialchars($myrow['lastname'])."</a></td>";
		print "<td NOWRAP>".htmlspecialchars($myrow['department'])."</td>";
		print "</tr>\n";
	}
	print "</table>\n\n<p>\n\n";


	// Query for inactive users

	$query = "SELECT * FROM user_tbl WHERE active='f' ORDER BY ".$sort_by." ".$order;

	$result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }

	print "<table>\n";
	print "<tr><td colspan=3>FORMER USERS</td></tr>\n";
	print "<tr><td> ID </td><td> Name </td><td> Department </td></tr>\n";

	while($myrow = pg_fetch_assoc($result)) {
		print "<tr>";
		print "<td NOWRAP><a href=\"user_details.php?user=".$myrow['userid']."\">".$myrow['userid']."</a></td>";
		print "<td NOWRAP><a href=\"user_details.php?user=".$myrow['userid']."\">".htmlspecialchars($myrow['firstname'])." ".htmlspecialchars($myrow['lastname'])."</a></td>";
		print "<td NOWRAP>".htmlspecialchars($myrow['department'])."</td>";
		print "</tr>\n";
	}
	print "</table>\n\n</body>\n</html>";

?>
