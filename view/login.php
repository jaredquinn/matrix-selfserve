<form method="post" id="frm-login">
	<h1>Matrix Login</h1>
	<? if($error): ?>
	<h3 class="error"><?= $error ?></h3>
	<? endif; ?>
	<fieldset>
		<div><label for="username">Username</label><input name="username" type="text" value="<?= $_REQUEST['username'] ?>" /></div>
		<div><label for="password">Password</label><input name="password" type="password" /></div>

		<input type="submit" name="action" value="Login" />
	</fieldset>
</form>


