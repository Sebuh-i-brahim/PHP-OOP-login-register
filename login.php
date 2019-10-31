<?php
require_once "core/init.php";

if (Request::exist()) {
	if (Token::check(Request::get('_token'))) {
		$validate = new Validate();
		$validate->check(
			array(	
				'username' => $validate->set('required'),
				'password' => $validate->set('required')
			),
			Request::all()
		);
		if ($validate->passed()){
			$user = new User();
			$remember = (Request::get('remember') === 'on')? true : false;
			try{
				if($user->login($validate->data('username'), $validate->data('password'), $remember)){
					Redirect::to('index.php');
				}
			}
			catch(Exception $m){
				die($m->getMessage());
			}
		}else{
			$errors = $validate->errors();
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="container">
		<ul style="display: inline;">
			<li><a href="index.php">Home</a></li>
			<li><a href="Register.php">Register</a></li>
		</ul>
		<?php if(isset($errors)):?>
			<ul>
			<?php foreach($errors as $error):?>
				<li><?= $error;?></li>
			<?php endforeach;?>
			</ul>
		<?php endif;?>
		<form action="" method="post">
			<div class="field">
				<label class="label" for="username">Username:</label>
				<input type="text" name="username" id="username" class="input" autocomplete="off" value="<?=e(Request::get('username'))?>">
			</div><br>
			<div class="field">
				<label class="label" for="password">Password:</label>
				<input type="password" name="password" id="password" class="input" autocomplete="off">
			</div><br>
			<div class="field">
				<label class="label" for="remember"><input type="checkbox" name="remember" value="on"> Remember me</label>
			</div>
			<input type="hidden" name="_token" value="<?=Token::generate();?>">
			<div class="field">
				<button type="submit" class="button">Log In</button>
			</div>
		</form>
	</div>
</body>
</html>