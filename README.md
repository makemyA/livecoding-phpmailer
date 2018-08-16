# Devenir un pro du mail avec PHP Mailer
## Installation

Avant toute chose veuillez vérifier si composer est bien installé sur votre machine.
```
composer --version
```
Si ce n'est pas le cas, installez le avant de continuer le tutoriel (google est ton ami cher disciple)
Une fois composer installé, veuillez suivre les étapes suivantes:
1. Ouvrir l'invite de **cmd** et y taper ces instructions pour: 

    * Installer php/mailer
    ```
    composer require phpmailer/phpmailer
    ```
     * installer le package gmail(optionnel)
    ```
    composer require league/oauth2-client
    ```
1. Ouvrez votre dossier vendor créé lors de l'installation de composer. Vérifiez que le répertoire **php mailer** s'y retrouve ainsi que le fichier **autoload.php**.
## Création du répertoire de travail

Vous pouvez cloner le répo du tutoriel à cet emplacement => [livecoding_phpmailer]("https://github.com/makemyA/livecoding-phpmailer");
## Création du formulaire
Ouvrez le fichier **index.php**



```html
        <div>
            <label for="name-form">Veuillez indiquer votre nom</label>
            <input type="text" id="name-form" required autofocus placeholder="Nom" name="name" pattern="[a-zA-Z]{2,50}">
        </div>
        <div>
            <label for="firstname-form">Veuillez indiquer votre prénom</label>
            <input type="text" id="firstname-form" required placeholder="Prénom" name="firstname" pattern="[a-zA-Z]{2,50}">
        </div>
        <div>
            <label for="email-form">Veuillez indiquer votre adresse mail</label>
            <input type="email" id="email-form" required placeholder="@" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,8}$" >
        </div>
        <div>
        <label for="message">Veuillez indiquer votre message</label>
        <input id="message"type="text" name="message" placeholder="votre message">
        </div>
        <div>
```
```html
            <label for="upload-picture">Veuillez choisir une photo</label>
            <input type="file" name="picture"id="upload-picture" width="100" height="auto" accept=".png,.gif,.jpg,.jpeg" required>
        </div>
        <button type="submit" name="submit">ENVOYER</button>
    </form>
```
Nous voulons que l'utilisateur ne puisse uploader que des images ayant pour extension .png, .jpeg, .jpg ou encore .gif. Dans ce but **accept=...** va filtrer les image ayant l'extension souhaitée directement dans l'explorateur de fichier de l'utilisateur lorsque celui-ci tentera d'upload un fichier via notre formulaire. Gardez bien à l'esprit qu'il s'agit d'un garde-fou pour l'utilisateur à la manière des **pattern** pour les input de type **text**. 

!!! En aucun cas cette règle ne suffit pour sécurisé l'envoi de notre futur mail. Ces "barrières" peuvent en effet être cassée vu que nous sommes toujours pour le moment du coté client !!!
Pour sécuriser de manière plus efficace les données envoyées via notre formulaire, nous traiterons des étapes de sanitisation, validation lors du traitement du fichier **cible.php**.
## Création du fichier cible.php
Un lien utile! https://github.com/PHPMailer/PHPMailer

php mailer utilise ses propres fonctions. Il remplace donc la fonction **mail()** traditionnelle. Pour que ce changement soit effectif il faut ajouter en haut du fichier que l'on souhaite utiliser la classe PHPMAILER
```php
use PHPMailer\PHPMailer\PHPMailer;
require '../vendor/autoload.php';
```
De là nous pouvons commencer le traitement des infos du formulaire, du coté serveur cette fois.

afin de pouvoir traiter l'upload de notre image assurez vous d'avoir ajouter **enctype="multipart/form-data"** dans la balise form de notre formulaire html. Ce paramètre va permettre de traiter les fichiers **$_FILES** comme des **$_POST** ou des **$_GET**

```html
<form action="cible.php" method="post" enctype="multipart/form-data">
```
Revenons à notre fichier **cible.php**

Nous pouvons désormais créer toutes les variables dont nous aurons besoin pour le traitement et la réponse des infos.

```php
if(isset($_POST["submit"])){
/*set Variables*/
    $date = date("d-m-Y");
    $heure = date("H:i");
    $name = $_POST['name'];
    $firstname = $_POST['firstname'];
    $email = $_POST['email'];
```
A ce stade-ci il serait intéressant d'avoir une vue d'ensemble des variables conservées en mémoire par notre page cible à l'instant actuel.
Pour ce faire, faisons en sorte de les voir

```php
print_r($_FILES);
print_r($_POST);
```
 remplissez le formulaire une première fois et regardons ce que nous avons...

Nos POST sont bien enregistrés dans un tableau ainsi que les caractéristique de notre fichier uploadé.

Nous pouvons commencer l'étape de sanitisation
Celle-ci va permettre de purger nos variables de tous les caractères non admis, si il en reste malgré les regex mises en place dans le formulaire html lui-même.
```php
/*SANITIZE*/ /*supprimer les caractères non voulu*/

    $san_name = filter_var($name, FILTER_SANITIZE_STRING);
    $san_firstname = filter_var($firstname, FILTER_SANITIZE_STRING);
    $san_email = filter_var($email, FILTER_SANITIZE_EMAIL);
```
Si la sanitisation est impossible nous indiquons à l'utilisateur de remplir les champs correctement. Sinon nous pouvons valider nos variables pour qu'elle puisse être utilisée lors de l'envoi du ou des mails qui vous suivre.

```php
/*VALIDATE*/
if ($san_name === false) {
    $errors['name'] =  "Veuillez indiquer votre nom.";
  }else {
  $errors['nom'] = "";
  }
if ($san_firstname === false) {
    $errors['firstname'] =  "Veuillez indiquer votre prénom.";
  }else {
  $errors['firstname'] = "";
  }
if ($san_email === false || $email == '') {
  $errors['email'] =  "Veuillez indiquer votre email.";
}
else {
  $errors['email'] = "";
}

```
Faisons ensuite la même chose pour notre fichier uploadé. En analysant le **print_r $_FILES** effectué plus haut nous pouvons constater que le tableau retourné prenait 4 paramètres:
```
 Array ( [picture] => Array ( [name] => logo.png [type] => image/png [tmp_name] => C:\Users\henro\AppData\Local\Temp\phpD682.tmp [error] => 0 [size] => 15964 ) )
```

1. name: Reprend le nom du fichier avec son extension.
1. type: Donne le type de fichier et d'extension
1. tmp_name: Donne le chemin ou est stocké le fichier avant sa redirection
1. error: indique si le fichier est correctement uploadé
1. size: indique la taille du fichier

Si le fichier correspond aux conditions qu'on lui a imposé,
Nous pouvons maintenant le rediriger à l'emplacement fixe qu'on lui a réservé:

```php
if($_FILES["picture"]["type"] == 'image/jpeg' or $_FILES["picture"]["type"] == 'image/jpg'or $_FILES["picture"]["type"] == 'image/gif'or $_FILES["picture"]["type"] == 'image/png' ){
    $file = "img/" .basename($_FILES["picture"]["name"]); /*renseigne le répertoire d'ou l'image va être envoyée*/
    move_uploaded_file($_FILES["picture"]["tmp_name"], $file); /*bouge le temp vers le bon répertoire*/
 }else{
     echo "Veuillez choisir une image ayant pour extension .gif, .jpg, .jpeg,.png";
 }
```

A ce stade nous savons que toutes nos variables sont saine. Nous pouvons commencer à configurer notre envoi par mail

Pour ce faire nous avons besoin de configurer notre serveur smtp qui va agir un peu comme un facteur pour diriger les mails à leur bonne destination.
Attention que si vous utilisez votre vraie adresse de messagerie. Un fichier .gitignore sera nécessaire pour  cacher les informations confidentielles comme le mot de passe de votre adresse de messagerie.
```php
$mail = new PHPMailer;                              // Passing `true` enables exceptions
        //Server settings
        $mail->isSMTP();                                      // Set mailer to use SMTP
       /*  $mail->SMTPDebug = 0;          */                        // Enable verbose debug output
        $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'livecoding.becode@gmail.com';                      // SMTP username
        $password = 'jesuisunmdp_0';                             //variable pour protÃ©ger le password
        $mail->Password = $password;                          // SMTP password
        //unset($password);
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted /*SSL et TLS sont deux protocoles cryptographiques qui permettent l’authentification, et le chiffrement des données qui transitent entre des serveurs, des machines et des applications en réseau (notamment lorsqu’un client se connecte à un serveur Web). Le SSL est le prédécesseur du TLS.*/
        $mail->Port = 587;
```

```php
//Sender
    $mail->setFrom('livecoding.becode@gmail.com', 'Paul Henrot');
    $mail->addAddress($email, $name);
    $mail->addCC('henrot.paul@hotmail.com', 'Admin');
```

```php
  /*Content*/
    $mail->isHTML(true);
    $mail->Subject = 'Live Coding PHP mailer';
    ob_start();/*démarre la temporisation de sortie. Tant qu'elle est enclenchée, aucune donnée, hormis les en-têtes, n'est envoyée au navigateur, mais temporairement mise en tampon.*/
    include("body.php");
    $mail->Body = ob_get_contents();
    ob_end_clean();  
    //Attachments
    $mail->addAttachment($file);
```
```php
if(!$mail->send()) {
        echo 'Message was not sent.';
        echo 'Mailer error: ' . $mail->ErrorInfo;
      } else {
         header('Location: confirmation.php');
      } 
```