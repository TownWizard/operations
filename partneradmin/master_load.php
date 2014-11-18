<?php
function checkValues($value)
{
	 // Use this function on all those values where you want to check for both sql injection and cross site scripting
	 //Trim the value
	 $value = trim($value);
	 
	// Stripslashes
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
	
	 // Convert all &lt;, &gt; etc. to normal html and then strip these
	 $value = strtr($value,array_flip(get_html_translation_table(HTML_ENTITIES)));
	
	 // Strip HTML Tags
	 $value = strip_tags($value);
	
	// Quote the value
	$value = mysql_real_escape_string($value);
	return $value;
	
}	
if($_POST['page'])
{
$page = $_POST['page'];
$cur_page = $page;
$page -= 1;
$per_page = 15;
$previous_btn = true;
$next_btn = true;
$first_btn = true;
$last_btn = true;
$start = $page * $per_page;
include"master-db.php";

$query_pag_data = "";
if(isset($_POST['selectedurl']) && $_POST['selectedurl'] !='')
{
	$update_data = "UPDATE master SET `partner_type` = 'paid' where site_url='".$_POST['selectedurl']."'";
	mysql_query($update_data);
	echo "<p style='color: green; position: absolute; font-weight: bold; left: 556px; font-size: 14px; background: none repeat scroll 0% 0% rgb(204, 204, 204); padding: 7px; top: 69px; border-radius: 5px;'>Upgraded Partner :".$_POST['selectedurl']." successfully";
	$_POST['selectedurl']='';
}
$query_pag_data .= "SELECT * from master";
if(isset($_POST['val']) && $_POST['val'] !='')
{
$rec = checkValues($_REQUEST['val']);
$query_pag_data .= " where (partner_type like '%$rec%') or (site_url like '%$rec%')";
}
$query_pag_data .= " ORDER BY mid desc LIMIT $start, $per_page";
//echo $query_pag_data;

$result_pag_data = mysql_query($query_pag_data) or die('MySql Error' . mysql_error());
$html = "";
while ($row = mysql_fetch_array($result_pag_data)) {
	//$html=htmlentities($row['message']);
    $html .= "<div class='data_con'>
				<div class='fl check'><input type='checkbox'  value=".$row['site_url']." name='cid' id='cb0'></div>
				<div class='fl first'>" . $row['mid']."</div>
				<div class='fl site'>". $row['site_url']."</div>
				<div class='fl'>". $row['tpl_folder_name']."</div>
				<div class='fl'>". $row['partner_type']."</div>
				<div class='fl'>". $row['style_folder_name']."</div>
				<div class='fl last'>". $row['partner_folder_name']."</div>
			</div>";
}
//$html = "<div class='data_con'>" . $html . "</div>"; // Content for Data


/* --------------------------------------------- */
$query_pag_num = "";
$query_pag_num .= "SELECT COUNT(*) AS count FROM master";
if(isset($_POST['val']) && $_POST['val'] !='')
{
$rec = checkValues($_REQUEST['val']);
$query_pag_num .= " where (partner_type like '%$rec%') or (site_url like '%$rec%')";
}
$result_pag_num = mysql_query($query_pag_num);
$row = mysql_fetch_array($result_pag_num);
$count = $row['count'];
$no_of_paginations = ceil($count / $per_page);

/* ---------------Calculating the starting and endign values for the loop----------------------------------- */
if ($cur_page >= 7) {
    $start_loop = $cur_page - 3;
    if ($no_of_paginations > $cur_page + 3)
        $end_loop = $cur_page + 3;
    else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
        $start_loop = $no_of_paginations - 6;
        $end_loop = $no_of_paginations;
    } else {
        $end_loop = $no_of_paginations;
    }
} else {
    $start_loop = 1;
    if ($no_of_paginations > 7)
        $end_loop = 7;
    else
        $end_loop = $no_of_paginations;
}
/* ----------------------------------------------------------------------------------------------------------- */
$html .= "<div class='pagination'><ul>";

// FOR ENABLING THE FIRST BUTTON
if ($first_btn && $cur_page > 1) {
    $html .= "<li p='1' class='active'>First</li>";
} else if ($first_btn) {
    $html .= "<li p='1' class='inactive'>First</li>";
}

// FOR ENABLING THE PREVIOUS BUTTON
if ($previous_btn && $cur_page > 1) {
    $pre = $cur_page - 1;
    $html .= "<li p='$pre' class='active'>Previous</li>";
} else if ($previous_btn) {
    $html .= "<li class='inactive'>Previous</li>";
}
for ($i = $start_loop; $i <= $end_loop; $i++) {

    if ($cur_page == $i)
        $html .= "<li p='$i' style='color:#fff;background-color:#006699;' class='active'>{$i}</li>";
    else
        $html .= "<li p='$i' class='active'>{$i}</li>";
}

// TO ENABLE THE NEXT BUTTON
if ($next_btn && $cur_page < $no_of_paginations) {
    $nex = $cur_page + 1;
    $html .= "<li p='$nex' class='active'>Next</li>";
} else if ($next_btn) {
    $html .= "<li class='inactive'>Next</li>";
}

// TO ENABLE THE END BUTTON
if ($last_btn && $cur_page < $no_of_paginations) {
    $html .= "<li p='$no_of_paginations' class='active'>Last</li>";
} else if ($last_btn) {
    $html .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
}
$goto = "<input type='text' class='goto' size='1' style='padding:2px 6px;height: 22px;'/><input type='button' id='go_btn' class='go_button' value='Go'/>";
$total_string = "<span class='total' a='$no_of_paginations'>Page <b>" . $cur_page . "</b> of <b>$no_of_paginations</b></span>";
$html = $html . "</ul>" . $goto . $total_string . "</div>";  // Content for pagination
echo $html;
}

