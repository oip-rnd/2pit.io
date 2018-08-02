<?php
$mail = 'bruno@p-pit.fr'; // Déclaration de l'adresse de destination.
if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui rencontrent des bogues.
{
	$passage_ligne = "\r\n";
}
else
{
	$passage_ligne = "\n";
}
//=====Déclaration des messages au format texte et au format HTML.
$message_txt = "
P-PIT Engagements
La pépite qui sécurise le respect de vos engagements réciproques.
Un reminder personnalisé et intelligent qui relie vos solutions existantes de gestion (commerciale, projet…) avec celles de vos clients et partenaires.
Le + : dématérialisez vos factures et contrôlez votre engagement de service.

Une solution efficace ET séduisante (dont terminaux mobiles), une intégration immédiate
La souplesse du modèle de location qui ne sacrifie pas la spécificité de mon besoin
Un modèle tarifaire win-win (proportionnel à l’activité confiée)

P-PIT Engagements : 0,5 € par mois et par engagement actif
";
$message_html = utf8_encode("
<html>
	<head>
	</head>
	<body>
		<h4 style=\"text-align: center; color: rgb(85, 85, 85); font-size: 18px;\">P-PIT Engagements</h4>
		<p style=\"text-align: left; color: rgb(85, 85, 85); font-size: 14px;\">La pépite qui sécurise le respect de vos engagements réciproques.</p>
		<p style=\"text-align: left; color: rgb(85, 85, 85); font-size: 14px;\"><span class=\"glyphicon glyphicon-calendar\"></span> Un reminder personnalisé et intelligent qui relie vos solutions existantes de gestion (commerciale, projet...) avec celles de vos clients et partenaires.</p>
		<p style=\"text-align: left; color: rgb(85, 85, 85); font-size: 14px;\"><span class=\"glyphicon glyphicon-plus\"></span> dématérialisez vos factures et contrôlez votre engagement de service.</p>
		<h4 style=\" style=\"text-align: left; color: rgb(85, 85, 85); font-size: 14px;\"\"><span class=\"glyphicon glyphicon-user\"></span></h4>
		<blockquote style=\"background: #f9#f9#f9; text-align: center; color: #555; font-size: 18px; font-style: italic\">
			Une solution efficace ET séduisante (dont terminaux mobiles), une intégration immédiate<br>
			La souplesse du modèle de location qui ne sacrifie pas la spécificité de mon besoin<br>
			Un modèle tarifaire win-win (proportionnel à l’activité confiée)
		</blockquote>
		<h4 style=\"text-align: center; color: rgb(85, 85, 85); font-size: 18px;\">P-PIT Engagements : 0,5 € par mois et par engagement actif</h4>
	</body>
</html>
");
//==========
 
//=====Création de la boundary
$boundary = "-----=".md5(rand());
//==========
 
//=====Définition du sujet.
$sujet = "P-PIT concourt en 2016 au Challenge Hack-tion Innocherche";
//=========
 
//=====Création du header de l'e-mail.
$header = "From: \"P-PIT\"<contact@p-pit.fr>".$passage_ligne;
$header.= "Reply-to: \"P-PIT\" <contact@p-pit.fr>".$passage_ligne;
$header.= "MIME-Version: 1.0".$passage_ligne;
$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
//==========
 
//=====Création du message.
$message = $passage_ligne."--".$boundary.$passage_ligne;
//=====Ajout du message au format texte.
$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
$message.= $passage_ligne.$message_txt.$passage_ligne;
//==========
$message.= $passage_ligne."--".$boundary.$passage_ligne;
//=====Ajout du message au format HTML
$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
$message.= $passage_ligne.$message_html.$passage_ligne;
//==========
$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
//==========
 
//=====Envoi de l'e-mail.
mail($mail,$sujet,$message,$header);
//==========
?>