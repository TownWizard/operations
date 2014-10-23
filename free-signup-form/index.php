<?php 
	session_start(); 
	$url = dirname($_SERVER['HTTP_REFERER']);
	$internalurl = "free-signup.townwizard.com";
	
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
	<link rel="stylesheet" href="css/style.css">
	<title>Initial Sign Up</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function(){
		
			$('.myButton').unbind().bind('click', myMethod);
			$('#contact').removeAttr('onsubmit');
			
			function myMethod(){
                 
				$('#contact').submit(function(e){   	

/**  JQUERY VALIDATION PROCESS  */
				
					var emailid = this.email.value;	

					if(emailid == "") { 
						alert("Email address required."); 
						this.email.focus();
						$('#contact').unbind('submit'); 
						return false;
					}else{
						var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
						if (filter.test(emailid)) {
						}else {
							alert("Valid email address required.."); 
							$('#contact').unbind('submit');
							this.email.focus();
							return false;
						}
					}	
									
/**  JQUERY VALIDATION PROCESS END */						
					
					var postData = $("#contact").serializeArray();
					var formURL = $("#contact").attr("action");
					var mailid = $("#email").val();
					
					$.ajax({
						url : formURL,
						type: "POST",
						data : postData,
						dataType : 'jsonp',
						success:function(data, textStatus, jqXHR){
					     if(data.status===100){
						  	window.location.href = 'http://<?php echo $internalurl;?>/index1.php?email='+mailid;
						}else if(data.status===101){
							$("#simple-msg").html('Guide with this email id is already activated.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #CCCCCC');
							$("#email").css('box-shadow','none');
						}else if(data.status===102){
							$("#simple-msg").html('This Email is registered in last 24 hours.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #FF5656');
							$("#email").css('box-shadow','0 0 2px #FF5656');
						}else if(data.status===103){
							window.location.href = 'http://<?php echo $internalurl;?>/index1.php?email='+mailid;	
						}else if(data.status===104){
							$("#simple-msg").html('This Guide name is already registered in last 24 hours.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #CCCCCC');
							$("#email").css('box-shadow','none');																
						}else if(data.status===105){
							$("#simple-msg").html('Guide already aaded but it is older than 24 hours and not activated yet so updating new email id.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #CCCCCC');
							$("#email").css('box-shadow','none');															
						}else if(data.status===106){
							$("#simple-msg").html('You could not be registered due to a system error. We apologize for any inconvenience.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #CCCCCC');
							$("#email").css('box-shadow','none');												
						}else if(data.status===107){
							$("#simple-msg").html('Please enter valid captcha.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #CCCCCC');
							$("#email").css('box-shadow','none');									
						}
                                                        
						$('#contact').unbind('submit');
						},
                             error: function(jqXHR, textStatus, errorThrown) {
                                  //  $("#simple-msg").html('<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus='+textStatus+', errorThrown='+errorThrown+'</code></pre>');
                             }
                        });

                    	e.preventDefault();
                    	return false;
               	});    
          	}
   		});
	</script>
<!--Google Analytic code-->
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-31932515-4', 'auto');
		ga('send', 'pageview');
	</script>
	
</head>
<body >
<div id="container">
	<h1>Create your town's guide</h1>
	<div id="stage1"></div>
	<span>Create Your Community's #1 Mobile Guide Business.</span>
	<form id="contact"  action="process.php" method="POST" class="validate-form1">
		<div id="simple-msg"></div>
		<input type="email" name="email" class="spemail" id="email" placeholder="Your email" required oninvalid="setCustomValidity('Valid email address required.')" onchange="try{setCustomValidity('')}catch(e){}" pattern="([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})" />
		<input type="submit" name="submit" class="myButton" id="Signup" value="Signup"  />
	</form>

</div>
</body>
</html>