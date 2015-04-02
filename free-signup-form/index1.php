<?php 
	//session_start(); 
	$url = dirname($_SERVER['HTTP_REFERER']);
	$internalurl = "free-signup.townwizard.com";
	
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
	<link rel="stylesheet" href="css/style.css">
	<title>Initial Sign Up</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
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
					var zip = $("#zip").val();
					var emailid = this.email.value;	
					var letters = /^[0-9a-zA-Z]+$/;
					if(this.fname.value == "" || !(this.fname.value.match(letters))) {
						alert("Please enter a valid first name. A-Z, a-z or 0-9 only.");
						this.fname.focus();
						$('#contact').unbind('submit');
						 return false;
					}

					if(this.lname.value == "" || !(this.lname.value.match(letters))) {
						alert("Please enter a valid last name. A-Z, a-z or 0-9 only.");
						this.lname.focus();
						$('#contact').unbind('submit');
						return false;
					}

					if(guidename == "" || !(guidename.match(letters))) {
						alert("Please enter a valid Guide name. A-Z, a-z or 0-9 only."); 
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
					
					if(zip == "" || !(zip.match(letters))) {
						alert("Please enter a valid Zip code. A-Z, a-z or 0-9 only."); 
						this.zip.focus();
						$('#contact').unbind('submit'); 
						return false;
					}else{
						if(zip.indexOf(" ") !== -1 ){
							alert("zip required. Blank spaces not allowed."); 
							this.zip.focus(); 
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
						  	window.location.href = 'http://<?php echo $internalurl;?>/thanks.html';
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
							window.location.href = 'http://<?php echo $internalurl;?>/thanks.html';						
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
						}else if(data.status===200){
							//console.log('else'); 
							$("#simple-msg").html('You could not be registered due to a system error. We apologize for any inconvenience.');
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
<?php

if (isset($_REQUEST['email'])){
	$email = $_REQUEST['email']; 
}else{
	echo '<div class="errormsgbox">Email is not Proper.</div>';
	exit;
}
if (isset($email)){	
?>
<div id="container">
	<h1>We need some information from you.</h1>
	
	<div id="stage2"></div>
	
	<form id="contact"  action="process1.php" method="POST" class="validate-form">
	    <div id="simple-msg"></div>
		<div class="row">
			<label for="fname">First Name<span class="require">*</span></label>
			<input type="text" name="fname" id="fname" placeholder="First Name" required oninvalid="setCustomValidity('Please enter a valid first name. A-Z, a-z or 0-9 only.')" onchange="try{setCustomValidity('')}catch(e){}" pattern="[a-zA-Z0-9\s]+" />
		</div>
			
		<div class="row">
			<label for="lname">Last Name<span class="require">*</span></label>
			<input type="text" name="lname" id="lname" placeholder="Last Name" required oninvalid="setCustomValidity('Please enter a valid last name. A-Z, a-z or 0-9 only.')" onchange="try{setCustomValidity('')}catch(e){}" pattern="[a-zA-Z0-9\s]+" />
		</div>
			
		<div class="row">
			<label for="website">Guide Name<span class="require">*</span></label>
			<input type="text" name="gname" id="gname" placeholder="Selected guide name" required oninvalid="setCustomValidity('Guide Name required. Blank spaces not allowed.')" onchange="try{setCustomValidity('')}catch(e){}" pattern="[a-zA-Z0-9]+" />
		</div>
		<span id="samplegname"></span>
		<div class="row">
			<label for="email">E-mail<span class="require">*</span></label>
			<input type="email" name="email" id="email" placeholder="yourname@domain.com" required oninvalid="setCustomValidity('Valid email address required.')" onchange="try{setCustomValidity('')}catch(e){}" value="<?php echo $email; ?>" pattern="([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})" readonly />
		</div>
		
		<div class="row">
			<label for="pass">Password<span class="require">*</span></label>
			<input type="password" name="pass" id="pass" size="15" placeholder="Password" required oninvalid="setCustomValidity('Password required. It should be 5 to 15 charater long.')" onchange="try{setCustomValidity('')}catch(e){}" pattern=".{5,15}" />	
		</div>	
		
		<div class="row">
			<label for="zip">City zip<span class="require">*</span></label>
			<input type="text" name="zip" id="zip" placeholder="City zip code" required oninvalid="setCustomValidity('Zip code required. A-Z, a-z or 0-9 only.')" onchange="try{setCustomValidity('')}catch(e){}" pattern="[a-zA-Z0-9]+" />
		</div>	
		
		<div class="row">
			<label for="language">Language</label>
			<select name="language" id="language">
				<option value="english">English</option>
				<option value="spanish">Spanish</option>
				<option value="dutch">Dutch</option>
				<option value="portuguese">Portuguese</option>
				<option value="croatian">Croatian</option>
				<option value="French">French</option>
			</select>
		</div>		
		
<!--		<div class="row">
			<label for="time_zone">Time zone</label>
			<select name="time_zone" id="time_zone" class="inputbox" size="1">
				<option value="-12:00:00">(GMT -12:00) Eniwetok, Kwajalein</option>
				<option value="-11:00:00">(GMT -11:00) Midway Island, Samoa</option>
				<option value="-10:00:00">(GMT -10:00) Hawaii</option>
				<option value="-9:00:00">(GMT -9:00) Alaska</option>
				<option value="-8:00:00">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
				<option value="-7:00:00">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
				<option value="-6:00:00">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
				<option value="-5:00:00" selected>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
				<option value="-4:00:00">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
				<option value="-3:30:00">(GMT -3:30) Newfoundland</option>
				<option value="-3:00:00">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
				<option value="-2:00:00">(GMT -2:00) Mid-Atlantic</option>
				<option value="-1:00:00">(GMT -1:00 hour) Azores, Cape Verde Islands</option>
				<option value="00:00:00">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
				<option value="1:00:00">(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
				<option value="2:00:00">(GMT +2:00) Kaliningrad, South Africa</option>
				<option value="3:00:00">(GMT +3:30) Tehran</option>
				<option value="4:00:00">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
				<option value="4:30:00">(GMT +4:30) Kabul</option>
				<option value="5:00:00">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
				<option value="5:30:00">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
				<option value="6:00:00">(GMT +6:00) Almaty, Dhaka, Colombo</option>
				<option value="7:00:00">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
				<option value="8:00:00">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
				<option value="9:00:00">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
				<option value="9:30:00">(GMT +9:30) Adelaide, Darwin</option>
				<option value="10:00:00">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
				<option value="11:00:00">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
				<option value="12:00:00">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
			</select>
		</div>
	
		<div class="row">
			<label for="time_format">Time format</label>
			<select name="time_format" id="time_format">
				<option value="12">12 - Hour Time</option>
				<option value="24">24 - Hour Time</option>
			</select>
		</div>		

		<div class="row">
			<label for="date_format">Date Format</label>
			<select name="date_format" id="date_format">
				<option value="mmdd">Month/Date</option>
				<option value="ddmm">Date/Month</option>
			</select>
		</div>			
		
		<div class="row">
			<label for="temperature">Temperature</label>
			<select name="temperature" id="temperature">
				<option value="c">Celsius</option>
				<option value="f" selected>Fahrenheit</option>
			</select>
		</div>			

		<div class="row">
			<label for="distance">Distance</label>
			<select name="distance" id="distance">
				<option value="KM">KM</option>
				<option value="Miles" selected>Miles</option>
			</select>
		</div>-->	
		
		<input type="hidden" value="<?php echo "-5:00:00"; ?>" name="time_zone" id="time_zone" />
		<input type="hidden" value="<?php echo "24"; ?>" name="time_format" id="time_format" />
		<input type="hidden" value="<?php echo "mmdd"; ?>" name="date_format" id="date_format" />
		<input type="hidden" value="<?php echo "f"; ?>" name="temperature" id="temperature" />
		<input type="hidden" value="<?php echo "Miles"; ?>" name="distance" id="distance"/>

		<div class="specialrow">
			<img src="captcha.php" id="captcha" style="width: 44%;vertical-align: middle;margin-left: 81px;" />
			<a href="#" onclick="document.getElementById('captcha').src='captcha.php?'+Math.random(); document.getElementById('captcha-form').focus();" id="change-image" style="color:#d72128; margin-left: 7px;">Refresh</a>
		</div>
		
		<div class="specialrow" >
			<label for="captcha">Add captcha</label>
			<input type="text" name="captcha" id="captcha-form" placeholder="captcha" autocomplete="off" required oninvalid="setCustomValidity('Please enter valid captcha')" onchange="try{setCustomValidity('')}catch(e){}" />
		</div>		
		
		<input type="hidden" value="<?php echo $internalurl; ?>" name="url" />
		
		<p>By clicking below to sign up, you are agreeing to the TownWizard <a href="http://townwizard.com/license.html" target="_blank" >License Agreement</a>.</p>
			
			<input type="submit" name="submit" class="myButton" id="Signup" value="Signup"  />
		</form>
</div>
<?php } ?>
</body>
</html>