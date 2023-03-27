
<form action="./connect.php" method="post">
	<div class = "form-style">
		<div>
			<input class = "answer" autofocus type="text" id="username" name="username" placeholder="Nom d'utilisateur" required>
		</div>
		<div>
			<input class = "answer" type="password" id="password" name="password" placeholder="Mot de passe" required>
		</div>
		<br>

        <button class = "form-button" type="submit">Se connecter</button>
	</div>
</form>

<div><button onclick = "openWindow('lost-password')" style = "background: none; border: none; margin-top: 1vw; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1vw;">Mot de passe oublié</button></div>