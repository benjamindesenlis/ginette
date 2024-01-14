<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/phpmailer/phpmailer/src/Exception.php';
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';

require '../../vendor/autoload.php';


$data = [];
$error = [];
$regex = '#^0[6-7]{1}\d{8}$#';

if (isset($_POST['submit2'])) {

    if (empty($_POST['name']) || strlen($_POST['name']) <= 3) {
        $error['name'] = ' Le champs nom est requis';
    } else {
        $name = htmlspecialchars($_POST['name']);
        $data['name'] = strip_tags($name);
    }

    if (!empty($_POST['tel']) && strlen($_POST['tel']) == 10) {
        if (!preg_match($regex, $_POST['tel'])) {
            $error['tel'] = ' Le champs ne correspond pas';
        } else {
            $data['tel'] = strip_tags($_POST['tel']);
        }
    } else {
        $error['tel'] = ' Le champs téléphone est requis ou ne correspond pas';
    }

    if (!empty($_POST['mail'] || strlen($_POST['mail'] <= 5))) {
        if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
            $email = htmlspecialchars($_POST['mail']);
            $data['mail'] = strip_tags($email);
        } else {
            $error['mail'] = "L'adresse mail est considérée comme invalide.";
        }
    }

    if (empty($_POST['message']) || strlen($_POST['message']) <= 3) {
        $error['message'] = 'Le champ message est requis et doit contenir plus de 3 caractères.';
    } else {
        $message = htmlspecialchars($_POST['message']);
        $data['message'] = strip_tags($message);
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ginette.capferret@gmail.com';
        $mail->Password = 'ewgg lxrf xgmk svmf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Utilisez l'adresse e-mail fournie dans le formulaire comme expéditeur
        $mail->setFrom($data['mail']);

        // Ajoutez l'adresse de destination (votre adresse Gmail)
        $mail->addAddress('ginette.capferret@gmail.com');

        $mail->isHTML(true);

        // Définir le sujet du courriel
        $mail->Subject = mb_encode_mimeheader('Message via Ginette.fr de ' . $data['name']);
        // $mail->Subject = mb_encode_mimeheader($data['name'] . ' (' . $data['mail'] . ') - Téléphone: ' . $data['tel']);


        // Corps du courriel
        $mail->Body = "Nom : " . $data['name'] .  "<br>Téléphone : " . $data['tel'] . "<br>Email : " .  $data['mail'] . "<br><br>Message :\n" . $data['message'];

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->SMTPDebug = 0;
        $mail->send();

        // Message envoyé avec succès - utilisez une alerte JavaScript
        echo '<script>alert("Message envoyé avec succès"); window.location.href = "../../index.php";</script>';
        exit(); // Arrêtez l'exécution du script après la redirection
    } catch (Exception $e) {
        // Message d'erreur
        echo '<script>alert("Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '"); window.location.href = "../../index.php";</script>';
        exit(); // Arrêtez l'exécution du script après la redirection
    }
} else {
    // Formulaire non soumis - éventuellement gérer cela comme vous le souhaitez
    echo '<script>alert("Formulaire non soumis"); window.location.href = "../../index.php";</script>';
    exit(); // Arrêtez l'exécution du script après la redirection
}
