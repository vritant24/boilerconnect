<h1>Class Member Look-up</h1>

// make a dropdown menu a list of CRNs in numerical order OR search system

<?php
$connection = mysqli_connect("localhost","root","","purdueConnect");

// $show needs to be modified to only show the selected CRNs or Class
$show = "SELECT * FROM fall2015 ORDER BY crn";
$result = mysqli_query($connection,$show);

echo "<table>
	<tr>
		// this will yield MA, ENGL, etc
		<th>Class Subject</th>
		// this will yield 16000A, 18000I, etc
		<th>Class Code</th>
		// this will yield CRNs
		<th>CRN</th>
		// this will yield Prof. name
		<th>Lecturer</th>
		// this will yield student name + hyperlink to their FB page
		<th>Classmate</th>
	</tr>";
while ($data=mysqli_fetch_array($result)){
	echo "<tr>
		<td>$data[coursename]</td>
		<td>$data[coursecode]</td>
		<td>$data[crn]</td>
		<td>$data[prof]</td>
		<td>$data[user]</td>
	</tr>";
}
echo "</table>";
?>
