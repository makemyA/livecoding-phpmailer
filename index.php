<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style/style.css" />
    <title>formulaire</title>
</head>
<body>
    <form action="cible.php" method="post" enctype="multipart/form-data"> <!-- allows a file to be launch as a POST -->
        <div class="formulaire">
            <label for="name-form">Veuillez indiquer votre nom</label>
            <input type="text" id="name-form" required autofocus placeholder="Nom" name="name" pattern="[a-zA-Z]{2,50}">
        </div>
        <div class="formulaire">
            <label for="firstname-form">Veuillez indiquer votre prénom</label>
            <input type="text" id="firstname-form" required placeholder="Prénom" name="firstname" pattern="[a-zA-Z]{2,50}">
        </div>
        <div class="formulaire">
            <label for="email-form">Veuillez indiquer votre adresse mail</label>
            <input type="email" id="email-form" required placeholder="@" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,8}$" >
        </div>
        <div class="formulaire">
        <label for="message">Veuillez indiquer votre message</label>
        <input id="message"type="text" name="message" placeholder="votre message">
        </div>
        <div class="formulaire">
            <label for="upload-picture">Veuillez choisir une photo</label>
            <input type="file" name="picture"id="upload-picture" width="100" height="auto" accept=".png,.gif,.jpg,.jpeg" required>
        </div>
        <button type="submit" name="submit">ENVOYER</button>
    </form>
</body>
</html>


