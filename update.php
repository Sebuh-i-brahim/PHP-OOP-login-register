<?php

require_once "core/init.php";

$user = new User();

if (!$user->islogged()) {
	Redirect::to('index.php');
}

if (Request::exist()) {
	if (Token::check(Request::get('_token'))) {
		$validate = new Validate();
		$validate->removeTag(Request::all())->check(array(
			"name" => (object)array(
				$validate->set('required'), 
				$validate->set('text'),
				$validate->set('max'),
				$validate->set('min')
			)
		));
		if ($validate->passed()) {
			try{
				$user->update($validate->data());
				Session::flash('home', "Your data has been updated!");
				Redirect::to('index.php');
			}catch(Exception $z){
				die($z->getMessage());
			}
		}else
		{
			$errors = $validate->errors();
		}

	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Update</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
	<ul>
		<li><a href="index.php">Index</a></li>
		<li><a href="changepassword.php">Change Your Password</a></li>
		<li><a href="logout.php">Log Out</a></li>
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
			<label class="label" for="name">Change your Name:</label>
			<input type="text" name="name" id="name" class="input" autocomplete="off" value="<?=e($user->data('name'));?>">
		</div><br>
		<input type="hidden" name="_token" value="<?=Token::generate();?>">
		<div class="field">
			<button type="submit" class="button">Update</button>
		</div>
	</form>
</div>
</body>
</html>