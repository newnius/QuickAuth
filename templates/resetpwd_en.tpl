<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="UTF-8"/>
		<title>[QuickAuth] Reset your QuickAuth password</title>
	</head>
	<body>
		<div style="width:450px; margin:0 auto; padding:25px; border: 1px solid #eee">
			<h3>Good day, <%username%> !</h3>
			<p>We received a request to reset your password in QuickAuth, just click the following button to finish it. If this request was not processed by you, please ignore it. Sorry for disturb.<a href="https://quickauth.newnius.com/help.php">Learn more about QuickAuth</a></p>
			<h4><a href="https://quickauth.newnius.com/resetpwd.php?key=<%auth_key%>">Reset passwordr</a></h4>
			<p>If the button doesn't work, copy following link to you browser and visit it.</p>
			<p><a href="https://quickauth.newnius.com/resetpwd.php?key=<%auth_key%>">https://quickauth.newnius.com/resetpwd.php?key=<%auth_key%></a></p>
			<p>This email is delivered by system automatically. If you have any problem, please contact <a href="mailto:support@newnius.com?subject='From QuickAuth'">support@newnius.com</a></p>
		</div>
	</body>
</html>
