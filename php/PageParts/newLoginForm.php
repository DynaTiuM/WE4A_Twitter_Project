<form action="./newAccount.php" method="post">
	
    <div class="formbutton">Créer votre compte</div>
    <br>
    <div>
        <label for="name">Nouveau Login : </label>
        <input autofocus type="text" id="name" name="name">
    </div>
    <div>
        <label for="password">Définir le mot de passe :</label>
        <input type="password" id="password" name="password">
    </div>
    <div>
        <label for="confirm">Confirmer le mot de passe :</label>
        <input type="password" id="confirm" name="confirm">
    </div>
    <br>
    <div class="formbutton">
        <button type="submit">Créer le compte</button>
    </div>
</form>