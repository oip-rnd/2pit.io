<?php
//On récupère le contenu brut du fichier xml modèle
$myContent = file_get_contents("PV-mise-en-service.xml");

//On remplace les mots-clés, un à un
$myContent = str_replace("@CONTRAT@", 'P-PIT - DYADEO', $myContent);
$myContent = str_replace("@PROJET@", 'UGAP', $myContent);
$myContent = str_replace("@OBJET@", 'Mise en service du lot 2', $myContent);
$myContent = str_replace("@DATE@", '01/06/2016', $myContent);
$myContent = str_replace("@PERIMETRE@", 'Mise en oeuvre du lot 2 conformément aux termes du contrat', $myContent);
$myContent = str_replace("@STATUT@", 'Livré', $myContent);
$myContent = str_replace("@PRESTATAIRE@", 'P-PIT', $myContent);
$myContent = str_replace("@CLIENT@", 'DYADEO', $myContent);

//On crée le fichier généré
$newFileHandler = fopen("PV-mise-en-service.doc","a");
fwrite($newFileHandler,$myContent);
fclose($newFileHandler);
