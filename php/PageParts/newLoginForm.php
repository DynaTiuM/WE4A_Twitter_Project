<form action="./newAccount.php" method="post" enctype="multipart/form-data">
	
    <div class="formbutton">Créer votre compte</div>
    <br>
    <div>
        <label for="username">Nom d'utilisateur : </label>
        <input autofocus type="text" id="username" name="username">
    </div>
    <div>
        <label for="email">E-mail :</label>
        <input type="text" id="email" name="email">
    </div>
    <div>
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom">
    </div>
    <div>
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom">
    </div>
    <div>
        <label for="date_de_naissance">Date de naissance :</label>
        <input type="date" id="date_de_naissance" name="date_de_naissance">
    </div>
    <div>
        <label for="password">Définir le mot de passe :</label>
        <input type="password" id="password" name="password">
    </div>
    <div>
        <label for="confirm">Confirmer le mot de passe :</label>
        <input type="password" id="confirm" name="confirm">
    </div>
    <div>
        <label for="organisation">Etes-vous une organisation ? :</label>
        <input type="radio" id="organisation" name="organisation">
    </div>
    <br>
    <div class="formbutton">
        <button type="submit">Créer le compte</button>
    </div>
</form>