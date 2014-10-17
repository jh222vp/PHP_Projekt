<?php

//Läser in filerna HTMLView samt LoginController.
require_once('View/HTMLView.php');
require_once('Controller/LoginController.php');

//Startar en session när en användare surfar in på hemsidan
session_start();

//view-variabeln öppnar anslutning till HTMLView och LC till LoginController-klassen.
$view = new HTMLView();
$LC = new LoginController();

//Kör funktionen doControl ifrån LoginController-klassen och..
$viewLogin = $LC->doController();
//skickar sedan med den till HTMLView-klassen där echoHTML tar emot svaret
$view->echoHTML($viewLogin);