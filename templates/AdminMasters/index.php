<script type="text/javascript">
	function trim(s) {
		while (s.substring(0, 1) === ' ') {
			s = s.substring(1, s.length);
		}
		while (s.substring(s.length - 1, s.length) === ' ') {
			s = s.substring(0, s.length - 1);
		}
		return s;
	}

	function front_user_validate() {
		const usr = document.getElementById('user_name1').value.trim();
		const pwd = document.getElementById('user_pass1').value.trim();

		if (usr === '' || usr === 'Username') {
			document.getElementById('login_error').innerHTML = '<font color="red">Please enter correct username</font>';
			document.getElementById('user_name1').focus();
			return false;
		} else if (pwd === '' || pwd === 'Password') {
			document.getElementById('login_error').innerHTML = '<font color="red">Please enter correct password</font>';
			document.getElementById('user_pass1').focus();
			return false;
		} else {
			document.getElementById('login_error').innerHTML = '';
			return true;
		}
	}
</script>

<?php
// In CakePHP 5, never use $this->request->data (it's protected).
// Use getData() safely.
$request = $this->request;
$txtUsername = 'Username';
$txtPassword = 'Password';

if (!empty($err) && $err == 1) {
	$txtUsername = (string)($request->getData('admin_user') ?? 'Username');
	$txtPassword = (string)($request->getData('admin_pass') ?? 'Password');
}
?>

<div id="login_container">
	<div class="login_inner">
		<div class="login_logodiv">
			<img src="<?= $this->Url->image('login_huracan_logo.png') ?>" title="Huracan" alt="Huracan" />
		</div>

		<div class="login_wrap">
			<?= $this->Form->create(null, [
				'type' => 'post',
				'id' => 'form',
				'onsubmit' => 'return front_user_validate();'
			]) ?>

			<h1>Admin Login</h1>

			<label>Username</label>
			<?= $this->Form->control('admin_user', [
				'type' => 'text',
				'value' => $txtUsername,
				'id' => 'user_name1',
				'maxlength' => 30,
				'label' => false,
				'div' => false,
				'onblur' => 'if(this.value === ""){this.value = "Username"}',
				'onfocus' => 'if(this.value === "Username"){this.value = ""}'
			]) ?>

			<div class="clrbotm"></div>

			<label>Password</label>
			<?= $this->Form->control('admin_pass', [
				'type' => 'password',
				'value' => $txtPassword,
				'id' => 'user_pass1',
				'maxlength' => 30,
				'label' => false,
				'div' => false,
				'onblur' => 'if(this.value === ""){this.value = "Password"}',
				'onfocus' => 'if(this.value === "Password"){this.value = ""}'
			]) ?>

			<div class="clrbotm"></div>

			<span id="login_error"><?= $login_error ?? '' ?></span>

			<input type="submit" name="button" value="Submit" class="buttonlogin fright" />

			<?= $this->Form->end() ?>

			<div class="clear"></div>
		</div>
	</div>
</div>
