<script language="php">

include("functions.php");

function decom_form_row($row_num,$pass_thq=NULL) {

	print "<tr>";
	print "<td><INPUT style=\"text-align: center\" SIZE=7 MAXLENGTH=5 TYPE=text NAME=\"thq$row_num\" VALUE=\"".$pass_thq."\"></td>";

	if ($pass_thq) {
		$query = "SELECT * FROM asset_tbl a
			LEFT OUTER JOIN pc_detail_tbl ON (pc_assetid=assetid)
			LEFT JOIN (
				SELECT DISTINCT ON (assetid_fkey) * FROM assignment_tbl
				LEFT JOIN user_tbl ON (userid_fkey=userid)
				ORDER BY assetid_fkey, datetime desc
			) AS usernames
			ON assetid=assetid_fkey
			WHERE thq=$pass_thq";
		$result = pg_query($query);
		error_check($result, $query);

		$row = pg_fetch_assoc($result);

		print "<td>&nbsp;";
		if ($row['computernumber']) { print "(".$row['computernumber'].") "; }
		print $row['description']."&nbsp;</td>";

		print "<td>".$row['firstname']."&nbsp;".$row['lastname']."</td>";
		print "<td>&nbsp;".$row['status']."&nbsp;</td>";

		print "<INPUT TYPE=hidden NAME=conf$row_num VALUE=$pass_thq>\n";
	} else {
		print "<td>&nbsp;</td>";
		print "<td>&nbsp;</td>";
		print "<td>&nbsp;</td>";
	}
	print "</tr>";
	return;

}

// MAIN

	$max_fields = 18; // Number of data entry rows available. This might become user defined in the future.
	$new_page = TRUE; // Assume the form is empty unless data is passed

	// Get passed data

	if ( isset($_POST['confirm']) ) { $confirmed = TRUE; }
	if ( isset($_POST['preview']) ) { $confirmed = FALSE; }

	// Clicking on the Clear button will skip the value inputs from the previous page, essentially clearing the values
	if ( !isset($_POST['clear']) ) {
		for ($i = 1; $i <= $max_fields; $i++) { // This section will go through each possible passed entry
			if ( isset($_POST["thq$i"]) ) { // Check to see if an entry exists
				$field[$i] = $_POST["thq$i"]; // Assign the entry to an array
				$new_page = FALSE;   // If any entry field has a value, it is not a new page
				if ( isset($_POST["conf$i"]) ) { // Check to see if the entry has a confirmation number
					$conf[$i] = $_POST["conf$i"]; // Assign the confirmation number to another array
					if ($field[$i] != $conf[$i]) { // Compare the entry to the corresponding confirmation number
						$confirmed=FALSE; // If *any* field fails to match the confirmation number, confirmation is denied
					}
				}
			}
		}
	}


	// Start page display

	include("jscript.html");
	html_header('HI Asset DB - Multi-Decomission','thq1');
	include("top_menu.php");

	print "<h1>Decomission Multiplicative Assets</h1>";
	print "<table class=outerframe>"; // Outer table
	print "<tr><td class=outerframe>\n";

	print "<form name=\"form\" method=\"post\" action=\"multi_decom.php\">\n"; // Form header

	print "<table class=noframe >\n"; // Inner table 1

	// Display Header row
	print "<tr>";
	print "<td style=\"width:50;\">THQ #</td>";
	print "<td NOWRAP style=\"width:250;\">Asset Description</td>";
	print "<td style=\"width:100;\">Owner</td>";
	print "<td style=\"width:50;\">Status</td>";
	print "</tr>\n";

	// Data rows

	if ( ($new_page) || (!$confirmed) ) { // For new pages and previews, show the form with spaces to enter data
		connect_to_db();

		// Print the data rows in the main table
		for ($i = 1; $i <= $max_fields; $i++) {
			decom_form_row($i,$field[$i]);
		}

		print "<tr><td colspan=4>\n\n<table align=center><tr>"; // Set up mini-table for buttons

		print "<td><INPUT TYPE=submit NAME=\"clear\" VALUE=\"Clear\"></td>\n";
		print "<td><INPUT TYPE=submit NAME=\"preview\" VALUE=\"Preview\"></td>\n";
		if (!$new_page) { print "<td><INPUT TYPE=submit NAME=\"confirm\" VALUE=\"Confirm\"></td>\n"; }

		print "</tr></table>\n\n"; // Close mini-table

		print "</td></tr></table>\n"; // Close Inner table 1
		print "</FORM>\n"; // End of form

		print "</td></tr></table>\n"; // Close Outer table

	} elseif ($confirmed) {  // If the fields all match and the preview button was not pressed, then the confirmed data is updated

		connect_to_db();

		for ($i = 1; $i <= $max_fields; $i++) { // $i,field[$i]

			if ($field[$i]) {

				// Set status to Decomissioned
				$update_query = "UPDATE asset_tbl SET active='f',status='Decomissioned' WHERE thq='".$field[$i]."'";

				$result = pg_query($update_query);
				error_check($result, $update_query);

				//Find assetid
				$query = "SELECT * FROM asset_tbl a
					LEFT OUTER JOIN pc_detail_tbl ON (pc_assetid=assetid)
					LEFT JOIN (
						SELECT DISTINCT ON (assetid_fkey) * FROM assignment_tbl
						LEFT JOIN user_tbl ON (userid_fkey=userid)
						ORDER BY assetid_fkey, datetime desc
					) AS usernames
					ON assetid=assetid_fkey
					WHERE thq='".$field[$i]."'";

				$result = pg_query($query);
				error_check($result, $query);
				$row = pg_fetch_assoc($result);

				// Set owner to None
				$insert_query = "INSERT INTO assignment_tbl (datetime,assetid_fkey,userid_fkey)
							VALUES ('".date('m/d/Y h:i:s A')."',".$row['assetid'].",'0')";
				$result = pg_query($insert_query);
				error_check($result, $insert_query);

				// Load data for display
				$query = "SELECT * FROM asset_tbl a
					LEFT OUTER JOIN pc_detail_tbl ON (pc_assetid=assetid)
					LEFT JOIN (
						SELECT DISTINCT ON (assetid_fkey) * FROM assignment_tbl
						LEFT JOIN user_tbl ON (userid_fkey=userid)
						ORDER BY assetid_fkey, datetime desc
					) AS usernames
					ON assetid=assetid_fkey
					WHERE thq='".$field[$i]."'";

				$result = pg_query($query);
				error_check($result, $query);

				$row = pg_fetch_assoc($result);

				// Print a line of data
				print "<tr>";

				// THQ# cell
				print "<td>&nbsp;".$row['thq']."&nbsp;</td>";

				// Description cell
				print "<td>&nbsp;";
				if ($row['computernumber']) { print "(".$row['computernumber'].") "; }
				print $row['description']."&nbsp;</td>";

				// Owner cell
				print "<td>".$row['firstname']."&nbsp;".$row['lastname']."</td>";

				// Status cell
				print "<td>&nbsp;".$row['status']."&nbsp;</td>";
				print "</tr>";

			}

		}

		print "<tr><td colspan=4>\n\n<table align=center><tr>"; // Set up mini-table for buttons
		print "<td><INPUT TYPE=submit NAME=\"more\" VALUE=\"Enter More\"></td>\n";
		print "</tr></table>\n\n"; // Close mini-table

	} else {

		print "<tr><td colspan=4>ERROR</td></tr>";

	}

	print "</tr></table>\n\n"; // Close mini-table

	print "</td></tr></table>\n"; // Close Inner table 1
	print "</FORM>\n"; // End of form

	print "</td></tr></table>\n"; // Close Outer table


// End of page

</script>
