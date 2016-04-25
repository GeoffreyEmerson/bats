<?php

	// Page timer
	$stimer = explode( ' ', microtime() );
	$stimer = $stimer[1] + $stimer[0];

	// Script start
	include("functions.php");
	include("jscript.html");
	html_header('HI Asset DB - Asset List','new_value');
	include("top_menu.php");

	connect_to_db();

	print "<table>\n";
	print "<tr><td> All Fields (Raw)</td></tr>\n";

	$query = "SELECT * FROM asset_tbl ORDER BY assetid DESC";

	$result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }

        while($myrow = pg_fetch_assoc($result)) {

		$imploded = implode("</td><td>", $myrow);
		printf ("<tr><td> %s </td></tr>\n", $imploded );

	}

	print "</table>\n>";

	//Display page timer
	$etimer = explode( ' ', microtime() );
	$etimer = $etimer[1] + $etimer[0];
	print "<br>\n<p style=\"margin:auto; text-align:left\">";
	print "<b>" . $row_count . " rows retrieved.</b><br>\n<br>\n";
	printf( "Page generated in <b>%f</b> seconds.", ($etimer-$stimer) );
	print "</p>\n";


	print "</body>\n>";
	print "</html>\n>";

?>
