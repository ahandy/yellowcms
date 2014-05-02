<html>
<head>
	<title>Yellow :: Content Management System</title>
	<link rel="stylesheet" href="<?php echo CSS_PATH; ?>cms/fonts.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo CSS_PATH; ?>cms/login.css" type="text/css" />
</head>

<body>

<div class='content'>
	<div class='login-form'>
		<div class='login-form-content'>
			<header>
				Yellow :: Content Management System :: Login
			</header>

			<form action='<?php echo HTTP; ?>login/process' method='post'>
				<input type='text' name='username' placeholder='Username' autocomplete="off" required />
				<input type='password' name='password' placeholder='Password' autocomplete="off" required />
				<input type='submit' value='Login' name='submit' data-process='<?php echo HTTP; ?>' />

				<span class='remember-me'>
					<input type='checkbox' name='remember' />
					<span></span>
				</span>
				
				<div class='clear'></div>
			</form>
		</div>
	</div>
</div>


<script src='<?php echo JS_PATH; ?>jquery/jquery.js' type='text/javascript'></script>
<script src='<?php echo JS_PATH; ?>yllw/login.js' type='text/javascript'></script>
</body>
</html>