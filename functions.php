<?php

$cell = '<td> %s </td>';

function connect_to_db() {

	// get database info from the heroku environment variable
	$db_env = parse_url(getenv('DATABASE_URL'));

	// build the database connection string
	$connect_string  = "host=" . $db_env["host"]
									 . " port=" . $db_env["port"]
									 . " dbname=" . ltrim($db_env["path"],'/')
									 . " user=" . $db_env["user"]
									 . " password=" . $db_env["pass"];
	$db = pg_connect($connect_string);

	// Testing the Heroku environment:
	// echo 'Database path: ' . $db_env["path"] . '<br/>';
	// echo 'Connection String: ' . $connect_string . '<br/>';

	// add code to respond to connection failures
}

function error_check($result,$query) {

        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
	    exit();
	}

}

function html_header($page_name,$focus) {
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
	    header('WWW-Authenticate: Basic realm="AssetDB"');
	    header('HTTP/1.0 401 Unauthorized');
	    echo 'Authorization Failed.';
	    exit;
	}

	print "<html><title>$page_name</title>\n";
	print "<head><link rel=\"stylesheet\" type=\"text/css\" href=\"assetdb.css\"></head>\n";
	if (!($focus == '')) {
		print "<body OnLoad=\"document.form.$focus.focus();\">\n\n";
	}
}

function nice($var) {
	switch($var) {

	case "thq":		return "THQ #";
	case "type":		return "Type";
	case "description":	return "Description";
	case "serial":		return "Serial";
	case "purchasedate":	return "Purchase Date";
	case "temp_assignment":	return "Assignment";
	case "computernumber":	return "Number";
	case "videocard":	return "Video Card";
	case "videobus":	return "Bus Type";
	case "cpu":		return "CPU Type";
	case "harddrive":	return "Hard Drive";
	case "memory":		return "Memory";
	case "servicetag":	return "Dell Tag";
	case "batch":		return "Batch";
	case "expresscode":	return "Express Code";
	case "warranty":	return "Warranty";
	case "mayadongle":	return "Maya Dongle";
	case "firstname":	return "First Name";
	case "lastname":	return "Last Name";
	case "department":	return "Department";
	case "extension":	return "Extension";
	case "status":		return "Status";
	case "user_status":	return "Status";
	case "active":		return "Active";

	default: return "NOT NICED: $var";
	}
}

function column_head($page, $pass_array, $sort_by, $column, $order, $default_sort) {

	if ($default_sort=="desc") {$anti_sort="asc";} else {$anti_sort="desc";}

	switch($column) { // This switch is for sort columns that aren't appropriately named for column headings.
		case "firstname":	$display="Assigned To";break;
		default:		$display=nice($column);
	}

	if ( ($sort_by == $column) && ($order == $default_sort) ) {
		print "<td><a href=\"".$page.".php?sort=".$column."&order=".$anti_sort;	} else {
		print "<td><a href=\"".$page.".php?sort=".$column."&order=".$default_sort; }
	if ( isset($pass_array['search']) ) {
		print "&search=".$pass_array['search']."&key=".htmlspecialchars($pass_array['key']); }

	print "\">".$display."</a></td>";

	return;
}

function detail_row($current_tbl, $asset, $current_val, $field, $edit_field) {

	if ($edit_field == $field) {

		print "<tr>\n";
		print "<td NOWRAP> ".nice($field)."</td>";
		print "<FORM name=\"update_form\" method=\"post\" action=\"updating.php\">\n";
		print "<INPUT TYPE=hidden NAME=pass_table VALUE=$current_tbl>\n";
		print "<INPUT TYPE=hidden NAME=pass_assetid VALUE=$asset>\n";
		print "<INPUT TYPE=hidden NAME=pass_field VALUE=$field>\n";
		print "<td NOWRAP ALIGN=center>\n";
		if ( ($field == "type") || ($field == "description") || ($field == "department") || ($field == "videocard") || ($field == "videobus") || ($field == "harddrive") || ($field == "cpu") || ($field == "memory") ) {

			print "<SELECT NAME=\"new_value\">\n";

			// Create OPTION list from current values in the database!
			$query = "SELECT $field FROM $current_tbl GROUP BY $field ORDER BY $field";
			$result = pg_query($query);
			while( $option_row = pg_fetch_assoc($result) ) {
				print "<OPTION VALUE=\"".htmlentities($option_row[$field])."\"";
				if ($option_row[$field] == $current_val) {print " selected=\"selected\"";}
				print ">".htmlentities($option_row[$field])."</OPTION>\n";
			}

			print "	</SELECT>\n";
			print "	</td>\n";

		} elseif ($field == "active") {
			print "<input type=radio NAME=\"new_value\" value=\"t\" ";
			if ($current_val=="t") { print "checked";}
			print ">Yes &nbsp;&nbsp;&nbsp;&nbsp;";
			print "<input type=radio NAME=\"new_value\" value=\"f\" ";
			if ($current_val=="f") { print "checked";}
			print ">No";
		} elseif ($field == "status") {
			$choice_array = array('Active','Available','Benched','Unstable','Proxy','Build','Remote','Parts','Reserved','Decomissioned','Unknown');
			print "<SELECT NAME=\"new_value\">\n";

			foreach ($choice_array as $choice) {
				print "<OPTION VALUE=\"". $choice ."\"";
					if ($choice == $current_val) { print "selected=\"selected\""; }
				print ">". $choice ."</OPTION>\n";
			}
			print "</SELECT>\n";
			print "</td>\n";

		} else {
			$value = htmlentities($current_val);
			print "<INPUT TYPE=text NAME=\"new_value\" VALUE=\"".$value."\"></td>\n";
		}
		print "<td><INPUT TYPE=submit VALUE=\"Update\"></td>\n";
		Print "</FORM>\n";
		print "</tr>\n\n";

		return;

	} else {		// Print data that is not currently being edited
		print "<tr>";
		print "<td NOWRAP> ".nice($field)."</td>";
		if ( ($field=="active") && ($current_val=='t') ) {
			print "<td style=\"color:green;\">Yes</td>";
		} elseif ( ($field=="active") && ($current_val=='f') ) {
			print "<td style=\"color:red;\">No</td>";
		} elseif ( ($field=="active") && (!$current_val) ) {
			print "<td style=\"color:red;\">NULL!</td>";
		} elseif ( ($field=="batch") || ($field=="harddrive") || ($field=="memory") || ($field=="videocard") || ($field=="videobus") || ($field=="cpu") || ($field=="warranty") || ($field=="status") ) { // Some fields get a reference to a list of other assets with the same value
			print "<td><a href=\"pc_list.php?search=$field&key=".htmlentities($current_val). "\">$current_val</a></td>";
		} elseif ($field=="description") {
			print "<td><a href=\"asset_list.php?search=$field&key=".htmlentities($current_val). "\">$current_val</a></td>";
		} else {
			print "<td> ".htmlentities($current_val)."</td>";
		}

		if ( ($current_tbl=="asset_tbl") || ($current_tbl=="pc_detail_tbl")       ) {
			print "<td NOWRAP class=noframe><a href=\"tag_details?asset=".$asset."&field=".$field."\">edit</a></td>";
		} elseif ($current_tbl == "user_tbl") {
			print "<td NOWRAP class=noframe><a href=\"user_details?user=".$asset."&field=".$field."\">edit</a></td>";
		}
		print "</tr>\n";

		return;
	}
}

function form_row($field) {

	print "<tr>";
	print "<td NOWRAP> ".nice($field)."</td>";
	print "<td NOWRAP ALIGN=center><INPUT TYPE=text NAME=\"new_$field\"></td>";
	print "</tr>\n";

	return;
}
