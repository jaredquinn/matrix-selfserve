<form method="post" id="frm-update">
	<h1>Matrix Update User</h1>
	<? if(isset($error)): ?>
	<h3 class="error"><?= $error ?></h3>
	<? endif; ?>
	<fieldset>
		<div><label for="username">Username</label><span class="view-only"><?= $_SESSION['username'] ?></span></div>


		<div><label for="new-password">New Password</label><input name="new-password" type="password" /></div>
		<? if(isset($err['new-password'])) { ?><div class="error"><?= $err['new-password'] ?></div><? } ?>

		<div><label for="try-password">Confirm Password</label><input name="try-password" type="password" /></div>
		<? if(isset($err['try-password'])) { ?><div class="error"><?= $err['try-password'] ?></div><? } ?>

		<div><label for="mobile">Mobile Number</label><input name="mobile" type="text" value="<?= $_REQUEST['mobile'] ?>" /></div>
		<? if(isset($err['mobile'])) { ?><div class="error"><?= $err['mobile'] ?></div><? } ?>

		<div><label for="mail">Email</label><input name="mail" type="text" value="<?= $_REQUEST['mail'] ?>" /></div>
		<? if(isset($err['mail'])) { ?><div class="error"><?= $err['mail'] ?></div><? } ?>

		<div><label for="domain">Domain</label><span class="view-only"><?= $_REQUEST['domain'] ? $_REQUEST['domain'] : '&nbsp;' ?></span></div>
		<div><label for="manager">Manager</label><span class="view-only"><?= $_REQUEST['manager'] ? $_REQUEST['manager'] : '&nbsp;' ?></span></div>

		<input type="submit" name="action" value="Update" />
	</fieldset>
</form>


