<html>
<head>
<link rel="stylesheet" type="text/css" href="assetdb.css" />
</head>
<body>



<?php

	include("top_menu.php");

	print "<table>\n";
	print "<tr><td> All Fields (Raw)</td></tr>\n";

	$db = pg_connect("host=$_ENV["HOST"] port=$_ENV["PORT"] dbname=$_ENV["DBNAME"] user=$_ENV["USER"] password=$_ENV["USER_PASS"]")
		or die('Could not connect: ' . pg_last_error());

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

?>

</table>
</body>
</html>
