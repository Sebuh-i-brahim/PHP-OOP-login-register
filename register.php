<?php
require_once "core/init.php";
if (Request::exist()) {
	if (Token::check(Request::get('_token'))) {
		$validate = new Validate();
		$validate->removeTag(Request::all())->check(
			array(
				'username' => (object)array($validate->set('required'),$validate->set('unique')),
				'email' => (object)array($validate->set('required'),$validate->set('email')),
				'password' => (object)array($validate->set('required'),$validate->set('password')),
				'password_again' => (object)array($validate->set('required'),$validate->set('match')),
				'name' => (object)array($validate->set('required'),$validate->set('text'))
			),
		);
		if($validate->passed()){
			$user = new User();
			$salt = Hash::salt(32);
			try{
				$user->create(array(
					'username' => $validate->data('username'),
					'email' => $validate->data('email'),
					'password' => Hash::make($validate->data('password'),$salt),
					'salt' => $salt,
					'name' => $validate->data('name'),
					'group_id' => 1
				));
				Session::flash('home', "You are in index");
				Redirect::to("index.php");
			}catch(Exception $v){
				die($v->getMessage());
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
	<title>Register</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
	<ul style="display: inline;">
		<li><a href="index.php">Home</a></li>
		<li><a href="login.php">Log in</a></li>
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
			<input type="text" name="username" id="username" class="input" value="<?= e(Request::get('username'));?>" autocomplete="off">
		</div><br>
		<div class="field">
			<label class="label" for="email">Email:</label>
			<input type="text" name="email" id="email" class="input" autocomplete="off" value="<?= e(Request::get('email'));?>">
		</div><br>
		<div class="field">
			<label class="label" for="password">Password:</label>
			<input type="password" name="password" id="password" class="input" autocomplete="off">
		</div><br>
		<div class="field">
			<label class="label" for="password_again">Password again:</label>
			<input type="password" name="password_again" id="password_again" class="input" autocomplete="off">
		</div><br>
		<div class="field">
			<label class="label" for="name">Name:</label>
			<input type="text" name="name" id="name" class="input" autocomplete="off" value="<?= e(Request::get('name'));?>">
		</div><br>
		<input type="hidden" name="_token" value="<?=Token::generate();?>">
		<div class="field">
			<button type="submit" class="button">Register</button>
		</div>
	</form>
</div>
</body>
</html>