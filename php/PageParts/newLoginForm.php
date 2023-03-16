<form action="./connect.php" method="post" enctype="multipart/form-data">
    <div class = "form-style">
        <div>
            <input class = "answer" autofocus type="text" id="username" name="username" placeholder="Nom d'utilisateur" required>
        </div>
        <div>
            <input class = "answer" type="text" id="email" name="email" placeholder="Adresse e-mail" required>
        </div>
        <div>
            <input class = "answer" type="text" id="prenom" name="prenom" placeholder="Prénom" required>
        </div>
        <div>
            <input class = "answer" type="text" id="nom" name="nom" placeholder="Nom" required>
        </div>
        <div>
            <label for="date_de_naissance">Date de naissance :</label> <br>
            <input class = "answer" type="date" id="date_de_naissance" name="date_de_naissance" required>
        </div>
        <div>
            <input class = "answer" type="password" id="password" name="password" placeholder="Mot de passe" required>
        </div>
        <div>
            <input class = "answer" type="password" id="confirm" name="confirm" placeholder="Confirmer le mot de passe" required>
        </div>
        <div>
            <label for="organisation">Etes-vous une organisation ? :</label>
            <label>
                <input type="radio" name="organisation" value="1" required>
                Oui
            </label>
            <label>
                <input type="radio" name="organisation" value="0">
                Non
            </label>

        </div>
        <br>
        <div class="formbutton">
            <button class = "form-button" type="submit">Créer le compte</button>
        </div>
    </div>

</form>