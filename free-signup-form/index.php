<?php 
	session_start(); 
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
	<link rel="stylesheet" href="css/style.css">
	<title>Initial Sign Up</title>
	<link rel="stylesheet" href="css/screen.css">
	<script src="js/jquery.js"></script>
	<script src="js/jquery.validate.js"></script>
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function(){
			
			$('#gname').blur(function() {
				$('#samplegname').empty();
				var string0 = "<b>Guide name = </b>";
				var string1 = $(this).val();
				var string2 = ".townwizard.com";
				$('#samplegname').append(string0).append(string1).append(string2);
			});
		
			$('.myButton').unbind().bind('click', myMethod);
			$('#contact').removeAttr('onsubmit');
			
			function myMethod(){
                 
				$('#contact').submit(function(e){   	

/**  JQUERY VALIDATION PROCESS  */
				
					var guidename = $("#gname").val();
					var emailid = this.email.value;	
				
					if(this.fname.value == "") {
						alert("Please enter a valid first name. A-Z, a-z or 0-9 only."); 
						this.fname.focus();
						$('#contact').unbind('submit');
						 return false;
					}

					if(this.lname.value == "") {
						alert("Please enter a valid last name. A-Z, a-z or 0-9 only."); 
						this.lname.focus();
						$('#contact').unbind('submit'); 
						return false;
					}
					
					
					if(guidename == "") { 
						alert("Guide Name required. Blank spaces not allowed."); 
						this.gname.focus();
						$('#contact').unbind('submit'); 
						return false;
					}else{
						if(guidename.indexOf(" ") !== -1 ){
							alert("Guide Name required. Blank spaces not allowed."); 
							this.gname.focus(); 
							$('#contact').unbind('submit');
							return false;
						}
					}
					
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
					
					if(this.pass.value == "") { 
						alert("Password required. It should be 5 to 15 charater long."); 
						this.pass.focus(); 
						$('#contact').unbind('submit');
						return false; 
					}else{
						var count = $("#pass").val().length;
						if(count < 5 || count > 15){
							alert("Password required. It should be 5 to 15 charater long"); 
							this.pass.focus(); 
							$('#contact').unbind('submit');
							return false;
						}
					}
					
					if(this.captcha.value == "") {
						alert("Please enter valid captcha.."); 
						this.captcha.focus();
						$('#contact').unbind('submit'); 
						return false;
					}				
		
				
/**  JQUERY VALIDATION PROCESS END */						
					
					
                        
                        var postData = $("#contact").serializeArray();
                        var formURL = $("#contact").attr("action");
					
					$.ajax({
						url : formURL,
						type: "POST",
						data : postData,
						dataType : 'jsonp',
						success:function(data, textStatus, jqXHR){
						//console.log(data);
						//console.log(textStatus);
					     if(data.status===100){
						  	window.location.href = 'http://<?php echo $_SERVER['HTTP_HOST'];?>/form/thanks.html';
							//$("#simple-msg").html('sucessfull.');
							//$("#simple-msg").css('color','red');
						}else if(data.status===101){
							//console.log('else');
							$("#simple-msg").html('Guide already activated.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #CCCCCC');
							$("#email").css('box-shadow','none');
							$("#gname").css('border','1px solid #FF5656');
							$("#gname").css('box-shadow','0 0 2px #FF5656');
						}else if(data.status===102){
							//console.log('else'); 
							$("#simple-msg").html('This Email is registered in last 24 hours. So you can not submit new guide name.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #FF5656');
							$("#email").css('box-shadow','0 0 2px #FF5656');
							$("#gname").css('border','1px solid #CCCCCC');
							$("#gname").css('box-shadow','none');	
						}else if(data.status===103){
							//console.log('else'); 
							window.location.href = 'http://<?php echo $_SERVER['HTTP_HOST'];?>/form/thanks.html';						
						}else if(data.status===104){
							//console.log('else'); 
							$("#simple-msg").html('This Guide name is already registered in last 24 hours.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #CCCCCC');
							$("#email").css('box-shadow','none');	
							$("#gname").css('border','1px solid #FF5656');
							$("#gname").css('box-shadow','0 0 2px #FF5656');															
						}else if(data.status===105){
							//console.log('else'); 
							$("#simple-msg").html('Guide already aaded but it is older than 24 hours and not activated yet so updating new email id.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #CCCCCC');
							$("#email").css('box-shadow','none');	
							$("#gname").css('border','1px solid #CCCCCC');
							$("#gname").css('box-shadow','none');																
						}else if(data.status===106){
							//console.log('else'); 
							$("#simple-msg").html('You could not be registered due to a system error. We apologize for any inconvenience.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #CCCCCC');
							$("#email").css('box-shadow','none');	
							$("#gname").css('border','1px solid #CCCCCC');
							$("#gname").css('box-shadow','none');											
						}else if(data.status===107){
							//console.log('else'); 
							$("#simple-msg").html('Please enter valid captcha.');
							$("#simple-msg").css('color','red');
							$("#email").css('border','1px solid #CCCCCC');
							$("#email").css('box-shadow','none');	
							$("#gname").css('border','1px solid #CCCCCC');
							$("#gname").css('box-shadow','none');									
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
</head>
<body >
<div id="logo"><img src="http://www.townwizard.com/wp-content/themes/5506/images/2012/header/townwizard_logo.png" alt="townwizard logo"></div>
<div id="simple-msg"></div>

<form id="contact"  action="process.php" method="POST" class="validate-form">
    
		<label for="fname">First Name</label>
		<input type="text" name="fname" id="fname" placeholder="First Name" required oninvalid="setCustomValidity('Please enter a valid first name. A-Z, a-z or 0-9 only.')" onchange="try{setCustomValidity('')}catch(e){}" pattern="[a-zA-Z0-9\s]+" />
		
		<label for="lname">Last Name</label>
		<input type="text" name="lname" id="lname" placeholder="Last Name" required oninvalid="setCustomValidity('Please enter a valid last name. A-Z, a-z or 0-9 only.')" onchange="try{setCustomValidity('')}catch(e){}" pattern="[a-zA-Z0-9\s]+" />
		
		<label for="website">Guide Name</label>
		<input type="text" name="gname" id="gname" placeholder="Selected guide name" required oninvalid="setCustomValidity('Guide Name required. Blank spaces not allowed.')" onchange="try{setCustomValidity('')}catch(e){}" pattern="[a-zA-Z0-9]+" /><span id="samplegname"></span>
	
		<label for="email">Guide Login E-mail</label>
		<input type="email" name="email" id="email" placeholder="yourname@domain.com" required oninvalid="setCustomValidity('Valid email address required.')" onchange="try{setCustomValidity('')}catch(e){}" pattern="([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})" />
		
		<label for="pass">Guide Login Password</label>
		<input type="password" name="pass" id="pass" size="15" placeholder="Password" required oninvalid="setCustomValidity('Password required. It should be 5 to 15 charater long.')" onchange="try{setCustomValidity('')}catch(e){}" pattern=".{5,15}" />		
		
		<img src="captcha.php" id="captcha" style="margin-left: 39px;margin-top: 5px;" /><br/>
		<a href="#" onclick="document.getElementById('captcha').src='captcha.php?'+Math.random(); document.getElementById('captcha-form').focus();" id="change-image" style="color: rgb(102, 102, 102); text-decoration: none; text-align: center; margin-left: 97px;">Refresh Text.</a><br/><br/>

		<label for="captcha">Add captcha word:</label>
		<input type="text" name="captcha" id="captcha-form" placeholder="captcha" autocomplete="off" required oninvalid="setCustomValidity('Please enter valid captcha')" onchange="try{setCustomValidity('')}catch(e){}" /><br/>
		
		<input type="submit" name="submit" class="myButton" id="Signup" value="Signup"  />
	</form>

</body>
</html>