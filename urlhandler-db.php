<?php
//Voorbeelden
//'folder/file'        => "/(example\/?(?'param'open|dicht)|example)"
//Voorbeeld regular expressions
//(?[0-9]{1,6})       Alleen cijfers minmaal 1 max 6 lang
//(?[a-zA-Z])         Alleen letters klein en groot
//(?'param'[a-z])     Zet er param voor zoals hier en hij wordt in $settings['extraParam'] gezet

//Config includen zodat die overal beschikbaar is
//Als je dat niet wilt hier alleen de connection file includen
//En hem dan uit de config bestand halen
require('static/config.php');

/*
Maak een database met minimaal de culomen page, url
@page   Alles vanaf de map pages naar de file zonder extencie
@url    De url die achter het domein staat

*/
//Connectie naar de database ophalen
$conn = Connection::getPDOconn();
$query = $conn->prepare("SELECT page, url
    FROM siteUrls");
$query->execute();

$rules = array();
while ($record = $query->fetch(PDO::FETCH_ASSOC)) {
    $rules[] = $record;
}

//Split de url en kijkt later met regular expressions controleren of het overeen komt
$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), '/' );
$uri = '/' . trim( str_replace( $uri, '', $_SERVER['REQUEST_URI'] ), '/' );

//Zorgt er voor dat alles met een ? er uit word gehaald
$uri = strtok($uri, '?');
$uri = urldecode( $uri );

foreach ( $rules as $action => $rule ) {
    //Kijkt met regular expressions of de ingevoerde url overeen komt met iets uit de database
    if ( preg_match( '~^'. $rule['url'] .'$~i', $uri, $params ) ) {

        //Kijkt of je niet uitgelogd moet worden
        if ($rule['page'] != 'logout') {
            //De pagina die op dit moment actief is opslaan in de settings
            $settings['activepage'] = $rule['page'];
            //Als je een param hebt in de url met de naam 'param' wordt die hier ingevuld
            $settings['extraParam'] = isset($params['param']) != '' ? $params['param'] : '';


            //front office includen als die bestaat voor die file
            if (file_exists(ROOT_PATH . 'data/front/' . $rule['page'] . '.php')) {
                require (ROOT_PATH . 'data/front/' . $rule['page'] . '.php');
            }

            ?>
            <!DOCTYPE html>
            <html lang="nl">
                <head>
                    <meta charset="utf-8">
                    <title> <?= $getText['siteTitle'] ?></title>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <!-- for Google -->
                    <meta name="description"            content="" />
                    <meta name="keywords"               content="" />
                    <meta name="copyright"              content="" />
                    <meta name="application-name"       content="" />

                    <?php
                    //Alle css includen
                    require(ROOT_PATH . 'static/headerload.php');

                    ?>
                </head>
                <body>

                    <?php
                    //Hier zou je ook nog een standaard header kunnen includen die dan in de static map staat


                    //Kijkt of het niet de error is (die pagina wordt ergens anders opgeslagen)
                    if ($rule['page'] != 'error404') {
                        //De pagina includen
                        require(PAGES_PATH .  $rule['page'] . '.php' );
                        //Voor als je de extensie ook nog dynamische wil
                        //Hoef je alleen maar een column extra te maken met de naam 'ext'
                        //require(PAGES_PATH .  $rule['page'] . $rule['ext'] );
                    } else {
                        require(ERROR_PATH . '404.php' );
                    }

                    //Alle js includen
                    //Als laast doen om de pagina sneller te laten laden
                    require(ROOT_PATH . 'static/footerload.php');


                    //Hier zou je ook nog een standaard footer kunnen includen die dan in de static map staat
                    ?>

                </body>
            </html>
            <?php

            //afsluiten anders laat hij de 404 pagina zien
            exit();
        } else {
            //Destroyd alle sessions
            session_destroy();
            //Verwijderd de cookie
            setcookie("userInfo", "" , time() -2592000 , "/");
            //Laat hem terug gaan naar de home pagina
            header("Location:" . WEBSITE_PATH);
            //afsluiten om er zeker van te zijn dat die niks anders meer gaat doen
            exit();
        }
    }

}

header("Location:" . 'https://domein.nl/404');
exit();
