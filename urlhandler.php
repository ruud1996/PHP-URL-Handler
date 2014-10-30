<?php
//Voorbeelden
//'folder/file'        => "/(example\/?(?'param'open|dicht)|example)"
//Voorbeeld regular expressions
//(?[0-9]{1,6})       Alleen cijfers minmaal 1 max 6 lang
//(?[a-zA-Z])         Alleen letters klein en groot
//(?'param'[a-z])     Zet er param voor zoals hier en hij wordt in $settings['extraParam'] gezet

//LET OP: Altijd eindigen met een , behalve bij de laatste
$rules = array(
    'index'                     => "/",

    'logout'                    => "/logout",
    'error404'                  => "/404"
);

//Split de url en kijkt later met regular expressions controleren of het overeen komt
$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), '/' );
$uri = '/' . trim( str_replace( $uri, '', $_SERVER['REQUEST_URI'] ), '/' );

//Zorgt er voor dat alles met een ? er uit word gehaald
$uri = strtok($uri, '?');
$uri = urldecode( $uri );

foreach ( $rules as $action => $rule ) {
    //Kijkt met regular expressions of de ingevoerde url overeen komt met iets uit de database
    if ( preg_match( '~^'. $rule .'$~i', $uri, $params ) ) {

        //Config includen zodat die overal beschikbaar is
        require('static/config.php');

        //Kijkt of je niet uitgelogd moet worden
        if ($action != 'logout') {
            //De pagina die op dit moment actief is opslaan in de settings
            $settings['activepage'] = $action;
            //Als je een param hebt in de url met de naam 'param' wordt die hier ingevuld
            $settings['extraParam'] = isset($params['param']) != '' ? $params['param'] : '';


            //front office includen als die bestaat voor die file
            if (file_exists(ROOT_PATH . 'data/front/' . $action . '.php')) {
                require (ROOT_PATH . 'data/front/' . $action . '.php');
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
                    if ($action != 'error404') {
                        //De pagina includen
                        require(PAGES_PATH .  $action . '.php' );
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
