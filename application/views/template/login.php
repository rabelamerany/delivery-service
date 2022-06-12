<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title><?php echo $title; ?></title>
		<link href="<?php echo base_url('assets/css/login/style.css') ?>" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<div id="wrap">

			<div id="header">
				<!--<p>logo CRM</p>-->
				<img src="assets/images/logo.png" alt="CRM logo">
			</div>
			<?php if(isset($error) && !empty($error)): ?>
				<p class="alert-danger"><?php echo $error; ?></p>
			<?php endif; ?>
			<div id="form">
				<form action="login" method="POST">
					<input type="text" name="username" placeholder="Username" required>
				    <input type="password" name="password" placeholder="Password" required>
				    <button name="loginSubmit" type="submit">Sign in</button>
				</form>
			</div>
		</div>
	</body>
</html>
