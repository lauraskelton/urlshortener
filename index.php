<?php
    // These variables define the connection information for your MySQL database
    $username = "username";
    $password = "password";
    $host = "host";
    $dbname = "dbname";
    
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
    try { $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); }
    catch(PDOException $ex){ die("Failed to connect to the database: " . $ex->getMessage());}
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    header('Content-Type: text/html; charset=utf-8');
    session_start();
    
    #split the path by '/'
    $shorturl     = str_replace("/", "", $_SERVER['REQUEST_URI']);
    
    if ((strlen($shorturl) > 0) && $shorturl != "shorten") {
        # should redirect to this url
        
        $query = "
        SELECT
        id,
        short,
        url
        FROM urllinks
        WHERE
        short = :shortlink
        ";
        $query_params = array(
                              ':shortlink' => $shorturl
                              );
        
        try{
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $goturl_ok = false;
        $row = $stmt->fetch();
        if($row){
            $goturl_ok = true;
        }
        
        if($goturl_ok){
            # redirect to the url
            header("Location: " . $row[url]);
            die("Redirecting to: " . $row[url]);
        } else{
            print("Redirect Failed. No such link.");
        }
        
    } else {
        
echo <<<EOLLAURA
        
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->


<head>
<!-- site charset -->
<meta charset="utf-8">
<!-- site title -->
<title>URL Shortener</title>
<!--  Meta  -->
<meta name="description" content="URL Shortener">
<meta name="author" content="Laura Skelton">
<!-- viewport for mobile devices -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<!-- site stylesheets -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/reset.css"> <!-- CSS reset -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/bootstrap.min.css"> <!-- Twitter Bootstrap -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/bootstrap-responsive.min.css"> <!-- Twitter Bootstrap (responsive) -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/prettyPhoto.css"> <!-- Lightbox gallery -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/style.css"> <!-- main stylesheet (change this to modify template) -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/style-responsive.css"> <!-- main stylesheet (responsive) -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/flexslider.css"> <!-- Flex slider -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/theme-options.css"> <!-- Theme options -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/color-themes/orange.css"> <!-- default color theme -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/textures/stripes.css"> <!-- default texture -->
<link rel="stylesheet" href="http://www.beerchooser.com/url-shortener/css/header-images/urban.css"> <!-- default header image -->
<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!--  IE8 fixes -->
<!--[if IE 8]>
  <link rel="stylesheet" href="css/ie8.css">
<![endif]-->
<!-- favicons -->
<link rel="shortcut icon" href="images/favicon.ico">

</head>
<body>
<div id="fb-root"></div>

<!-- Header -->
<section id="header">
  <div class="elevated">
    <div class="container">
      <div class="row">
        <div class="span12">
          <div id="logo">
            <a href="./"><img src="http://www.beerchooser.com/url-shortener/images/logo.png" alt="URL Shortener"></a>
            <p class="site-description">Shorten Those Links</p>
            <h1>URL Shortener</h1>
          </div>
        </div>
        <div id="teaser">
          
          <div class="span6">
            <div id="teaser-right">
              <h2>Links. Shorter.</h2>
    
EOLLAURA;

if(!empty($_POST['urltoshorten'])){
    
    if (filter_var($_POST['urltoshorten'], FILTER_VALIDATE_URL) === FALSE) {
        echo "<p>URL '" . $_POST['urltoshorten'] . "' is not valid.</p>";
    } else {
    
        function random_string($length = 6, $chars = '1234567890qwrtypsdfghjklzxcvbnmQWRTYPSDFGHJKLZXCVBNM')
        {
            $chars_length = (strlen($chars) - 1);
            $string = $chars{rand(0, $chars_length)};

            for ($i = 1; $i < $length; $i = strlen($string))
            {
                $r = $chars{rand(0, $chars_length)};
                $string .=  $r;
            }
            return $string;
        }
        
        $shortstring = random_string();
        
        $query = "
        INSERT INTO urllinks (short, url)
        VALUES (:shortstring, :urltoshorten)
        ";
        $query_params = array(
                              ':shortstring' => $shortstring,
                              ':urltoshorten' => $_POST['urltoshorten']
                              );
        
        try{
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $goturl_ok = false;
        $row = $stmt->rowCount();
        # share the new short url
        echo '<p>Shortened URL to <a href="http://prty.us/' . $shortstring . '">' . 'http://prty.us/' . $shortstring . '</a></p>';
    }
    
} else {

        echo '<p>URL Shortener takes your links, and makes them shorter. Try it out!</p>';
}
        
        echo <<<EOLLAURA

            <form method="post" action="http://prty.us/shorten" id="shortenurlform">
            <div>
        <label for="urltoshorten">Enter your URL to shorten:</label>
            <input type="text" class="input-field" id="urltoshorten" name="urltoshorten" value="">
                </div>
                <div>
            <input type="submit" class="btn btn-default">
            </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="texture"></div>
  <div class="clear"></div>
</section>
<footer>
  <div class="elevated">
    <div class="container">
      <div class="row">
        <div class="span12">
          <p class="copytext">&copy; 2014, <a href="#">Laura Skelton</a></p>
          <p class="outtro">Shorten your links!</p>
        </div>
      </div>
    </div>
  </div>
  <div class="texture"></div>
  <div class="clear"></div>
</footer>
        
<!-- JAVASCRIPTS
================================================== -->
<script src="http://www.beerchooser.com/url-shortener/js/jquery-1.8.2.min.js"></script><!-- jQuery main file -->
<script src="http://www.beerchooser.com/url-shortener/js/bootstrap.min.js" type="text/javascript"></script><!-- Twitter Bootstrap grid -->
<script src="http://www.beerchooser.com/url-shortener/js/jquery.prettyPhoto.js" type="text/javascript"></script><!-- Lightbox -->
<script src="http://www.beerchooser.com/url-shortener/js/jquery.flexslider.js" type="text/javascript"></script><!-- Flex slider -->
<script src="http://www.beerchooser.com/url-shortener/js/jquery.quovolver.js" type="text/javascript"></script><!-- Blockquote Revolver -->
<script src="http://www.beerchooser.com/url-shortener/js/jquery.tweet.js" type="text/javascript"></script><!-- Twitter feed -->
<script src="http://www.beerchooser.com/url-shortener/js/jquery.form.js" type="text/javascript"></script><!-- ajax form handler -->
<script src="http://www.beerchooser.com/url-shortener/js/common.js" type="text/javascript"></script><!-- Custom JS effects, tweaks and inits -->
<!-- End Document
================================================== -->

</body>
        </html>
        
EOLLAURA;
    }
    ?>