<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body style="background-color:rgba(29, 28, 28,0.9); color:white; text-align:center; font-size: 1.2em; width: 40%; height:auto; padding:1em">
<div>
    <p>Merci <?php echo $_POST["firstname"]?>,</p>
    <p>Nous avons bien reçu votre demande en date du <?php echo $date?> à <?php echo $heure?> concernant:</p>
    <p style="font-weight:700; font-size:1.5em"><?php echo $_POST["message"]?></p>
    <p>Nous vous répondrons dans les plus brefs délais</p>
    <p>L'équipe LIVE CODING</p>
</div>
</body>
</html>
