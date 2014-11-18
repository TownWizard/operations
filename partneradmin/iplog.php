<?php
session_start();
if(empty($_SESSION['login_user']))
{
header('Location: index.php');
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- This is a pagination script using Jquery, Ajax and PHP
     The enhancements done in this script pagination with first,last, previous, next buttons -->

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <title>Unlock IP Address</title>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script> 
        <script type="text/javascript">
            $(document).ready(function(){
                function loading_show(){
                    $('#loading').html("<img src='images/loading.gif'/>").fadeIn('fast');
                }
                function loading_hide(){
                    $('#loading').fadeOut('fast');
                }                
                function loadData(page,set){
                    loading_show();   
                    $.ajax
                    ({
                        type: "POST",
                        url: "load_data.php",
                        data: {
							"page":page,
							"val":set
							},
                        success: function(html)
                        {
                            $("#container").ajaxComplete(function(event, request, settings)
                            {
                                loading_hide();
                                $("#container").html(html);
                            });
                        }
                    });
                }
                loadData(1);  // For first time page load default results
				$('#search').keyup(function(e) {
				  if(e.keyCode == 13) {
					loading_show(); 				
					var set=$("#search").val();
					set=set.trim().replace(/ /g, '%20');
					loadData(1,set);
				  }
				});

				$(".searchBtn").click(function(){ 
					//show the loading bar
					loading_show();     
					var set=$("#search").val();
					set=set.trim().replace(/ /g, '%20');
					loadData(1,set);
				});
                $('#container .pagination li.active').live('click',function(){
                    var page = $(this).attr('p');
					var set=$("#search").val();
					set=set.trim().replace(/ /g, '%20');
                    loadData(page,set);
                    
                });
				function updateData(page,ip){
                    loading_show();   
                    $.ajax
                    ({
                        type: "POST",
                        url: "load_data.php",
                        data: {
							"page":page,
							"ip":ip
							},
                        success: function(html)
                        {
                            $("#container").ajaxComplete(function(event, request, settings)
                            {
                                loading_hide();
                                $("#container").html(html);
                            });
                        }
                    });
                }				
				$(".update").click(function(){
					//var selectedIp = new Array();
					var selectedIp = $('input[name="cid"]:checked').val();
					/*$('input[name="cid"]:checked').each(function() {
						selectedIp.push(this.value);
					});*/
					//alert("Number of selected IP: "+selectedIp);
					if(selectedIp!=null){
						updateData(1,selectedIp);
					}else{
						alert("Please Select IP address to unlock");
					}
					
				});
                $('#go_btn').live('click',function(){
                    var page = parseInt($('.goto').val());
                    var no_of_pages = parseInt($('.total').attr('a'));
                    if(page != 0 && page <= no_of_pages){
                        loadData(page);
                    }else{
                        alert('Enter a PAGE between 1 and '+no_of_pages);
                        $('.goto').val("").focus();
                        return false;
                    }
                    
                });
            });
        </script>
		
		<link rel="stylesheet" type="text/css" media="screen" href="css/iplog.css" />
       
    </head>
    <body>
	
<div id="main">
		
		<div class="menu_con">
			<a href="iplog.php" class="active">Unlock Ip address</a>	
			<a href="master.php">Upgrade Partner</a>
			<div><a href="logout.php" class="logout">Logout</a></div>
		</div>
		<div id="top_con">
	        <div class="textBox">
		        <input type="text" value="" maxlength="100" name="searchBox" id="search">
				<div class="searchBtn">&nbsp;</div>
			</div>
			
		</div>
		<div id="loading"></div>
		<div id="content">
			<div class="header_con">
				<div class="fl check">&nbsp;</div>
				<div class="fl title first">id</div>
				<div class="fl title">ip address</div>
				<div class="fl title">failed login count</div>
				<div class="fl title">first failed login_time</div>
				<div class="fl title">ip locked time</div>
				<div class="fl title">is ip locked</div>
				<div class="fl title last">site url</div>
			</div>
			<div id="container">
				
			</div>
			<div class='update'>Unlock IP</div>
		</div>
		</div>
</div>	
    </body>
</html>
