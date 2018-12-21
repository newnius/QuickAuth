<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="UTF-8" />
		<title>[QuickAuth] Welcome to QuickAuth</title>
	</head>
	<body>
		<div style="width:450px; margin:0 auto; padding:25px; border: 1px solid #eee">
			<h3>Welcome to QuickAuth, <%username%></h3>
			<p>Thank you for your registration! QuickAuth is a user system which supports OAuth. By using QuickAuth, you can access some websites without sign up for another account.<a href="<%base_url%>/help">Learn more about QuickAuth</a></p>
			<h4><a style="background:#5cb85c; width:200px; height:50px; font-size:larger;" href="<%base_url%>/verify?code=<%auth_key%>">Finish register</a></h4>
			<p>If the button doesn't work, copy following link to you browser and visit it.</p>
			<p><a href="<%base_url%>/verify?code=<%auth_key%>"><%base_url%>/verify?code=<%auth_key%></a></p>
			<p>This email is delivered by system automatically. If you have any problem, please contact <a href="mailto:support@newnius.com?subject='From QuickAuth'">support@newnius.com</a></p>
		</div>
	</body>
</html>
