<!DOCTYPE html>
  <html lang="en-US">
    <head>
      <meta charset="utf-8" />
      <title>Welcome to QuickAuth</title>
    </head>
    <body>
      <div style="width:450px; margin:0 auto; padding:25px; border: 1px solid #eee">
        <h3>Welcome to QuickAuth, <%username%></h3>
	<p>Thank you for your registeration! QuickAuth is an implement of authorization. By using QuickAuth, you can log in to some websites without sign up for another account, which most likely will be used only once. <a href="http://quickauth.newnius.com/help.php">Learn more about QuickAuth</a></p>
	<h4><button style="background:#5cb85c; width:200px; height:50px; font-size:larger;" onclick="javascript:window.location.href='http://quickauth.newnius.com/verify.php?key=<%auth_key%>'">Finish register</button></h4>
	<p>If the button doesn't work, copy following link to you browser and visit it.</p>
	<p><a href="http://quickauth.newnius.com/verify.php?key=<%auth_key%>">http://quickauth.newnius.com/verify.php?key=<%auth_key%></a></p>
        <p>This email is delevered by system automatically, please do not reply. If you have any problem, please contact <a href="mailto:support@newnius.com?subject='From QuickAuth'">support@newnius.com</a></p>
      </div>
    </body>
  </html>


