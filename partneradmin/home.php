<?php
session_start();
if(empty($_SESSION['login_user']))
{
header('Location: index.php');
}

?>
<?php
if (isset($_REQUEST['tab'])) {
        $tab = $_REQUEST['tab'];
    } else {
        $tab = 0;
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Controling CSS Tabs Using PHP</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
	$(document).ready(function() {
    $("#nav li a").click(function() {
 
        $("#ajax-content").empty().append("<div id='loading'><img src='images/loading.gif' alt='Loading' /></div>");
        $("#nav li a").removeClass('current');
        $(this).addClass('current');
 
        $.ajax({ url: this.href, success: function(html) {
            $("#ajax-content").empty().append(html);
            }
    });
    return false;
    });
 
    $("#ajax-content").empty().append("<div id='loading'><img src='images/loading.gif' alt='Loading' /></div>");
    $.ajax({ url: 'iplog.php', success: function(html) {
            $("#ajax-content").empty().append(html);
    }
    });
});
	
</script>
<style type="text/css">

/*Credits: Vijit Patil */

.tabZ{
padding: 3px 0;
margin-left: 0;
font: bold 12px Trebuchet MS ;
text-align: left;
border-bottom: 1px solid gray;
list-style-type: none;

}

.tabZ li{
display: inline;
margin: 0;
}

.tabZ li a{
    text-decoration: none;
    padding: 3px 7px;
    margin-right: 3px;
    border: 1px solid gray;
    border-bottom: none;
    background-color: #FDEAFD;
    color: #2d2b2b;
}

.tabZ li a:visited{
color: #2d2b2b;
}

.tabZ li a:hover{
    background-color: #FF8AFF;
    color: black;
}

.tabZ li a:active{
color: black;
}

.tabZ li.selected a{/*selected tab*/
   
position: relative;
    top: 1px;
    padding-top: 4px;
    background-color: #FF8AFF;
    color: black;
}


</style>
</head>

<body>
<ul id="nav">
  <li><a href="page_1.html">Page 1</a></li>
  <li><a href="iplog.php">Page 2</a></li>
  <li><a href="page_3.html">Page 3</a></li>
</ul>
 
<div id="ajax-content">This is default text, which will be replaced</div>
                   
</body>
</html>