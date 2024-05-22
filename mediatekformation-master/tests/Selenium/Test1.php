<?php

require 'vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;

// Adresse du Selenium server (remplacez par l'adresse de votre Selenium server)
$serverUrl = 'http://localhost:4444/wd/hub';

// Démarrer le navigateur (par exemple, Firefox)
$driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::firefox());

// Accéder à l'application Symfony en localhost
$driver->get('http://127.0.0.1:8000'); // ou remplacez par l'adresse IP trouvée si vous accédez depuis une autre machine

// Exemple d'action: Rechercher un élément et interagir avec lui
$element = $driver->findElement(WebDriverBy::name('search'));
$element->sendKeys('Selenium');
$element->submit();

// Fermer le navigateur
$driver->quit();
