<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<?php
//$q = intval($_GET['q']);
$i = 0;
$con = mysqli_connect('localhost','root','','missionw');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

$sql="SELECT frt,descServicio,fechaCronograma,estado,drive FROM facturacions ORDER BY `frt` ASC";
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) > 0) {
	while($row = mysqli_fetch_assoc($result)) {
echo "
									<tr data-status=\"".$row['estado']."\">
										<td>
											<div class=\"ckbox\">
												<input type=\"checkbox\" id=\"checkbox".$i."\" class=\"showSingle\" value=\"".$row['frt']."\" target=\"1\" onchange=\"showUser(this.value)\">
												<label for=\"checkbox".$i."\"></label>	
											</div>
										</td>";
echo "<td>
											<div class=\"media\">
												<a href=\"".$row['drive']."\" class=\"pull-left\">
													<img src=\"images/pdf.png\" class=\"media-photo\">
												</a>
												<div class=\"media-body\">
													<span class=\"media-meta pull-right\">".$row['fechaCronograma']."</span>
													<h4 class=\"title\">";
echo "<p>".$row['frt']."</p>";
echo "
											

											<span class=\"pull-right ".$row['estado']."\">(".$row['estado'].")</span>
													</h4>
													<p class=\"summary\"><small>".$row['descServicio']."</small></p>
												</div>
											</div>
										</td></tr>";
		$i++;
	}
} else {
    echo "0 results";
}
mysqli_close($con);
?>
</body>
</html>