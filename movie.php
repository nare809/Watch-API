<?php
ini_set('error_reporting', E_STRICT);
require_once("config.php");
$imdb = $_GET['imdb'] ?? null;

function vidcloud($resp)
{
 $linkArray = array();
 if(preg_match_all('/<a\s+.*?href=[\"\']?([^\"\' >]*)[\"\']?[^>]*><li>Mirror Server<\/li>(.*?)<\/a>/i', $resp, $matches, PREG_SET_ORDER)){
  foreach ($matches as $match) {
    array_push($linkArray, array($match[1]));
  }
 }
 return $match[1];
}

$url = "https://databasegdriveplayer.xyz/player.php?imdb=".$imdb;
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Accept: */*",
   "authority: databasegdriveplayer.xyz",
   "cache-control: no-cache",
   "pragma: no-cache",
   "origin: https://databasegdriveplayer.xyz",
   "referer: https://databasegdriveplayer.xyz/",
   "sec-fetch-dest: empty",
   "sec-fetch-mode: cors",
   "sec-fetch-site: same-origin",
   "sec-gpc: 1",
   "upgrade-insecure-requests: 1",
   "x-requested-with: XMLHttpRequest",
   "user-agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);
$string = vidcloud($resp);
$string = htmlspecialchars_decode($string);
$string = substr($string, 0, strpos($string, "&title"));

$json = $string;    
$whatIWant = substr($json, strpos($json, "=") + 1);    
require_once("./vendor/autoload.php");
use Wepesi\App\JWT;
$data=[
        "data"=>$whatIWant,
        "expired"=>3000
    ];
    $tken= new JWT;
    $_token=$tken->generate($data);
    $video=$_token;

$site = base64_encode($config_domain."/api/token.php?token=");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <meta name="robots" content="noindex, nofollow" />
        <meta name="referrer" content="never" />
        <meta name="referrer" content="no-referrer" />
        
        <title>Player</title>
        
        <script src="https://content.jwplatform.com/libraries/DkwOvSfA.js"></script>
        <script>var secret = "<?php echo $site; ?>"; var token = "<?php echo $video; ?>";</script>
        <link rel="stylesheet" href="<?php echo $config_domain; ?>/api/assets/css/style.css?ver=<?php echo strtotime('now'); ?>">
        <?php include('ads.php');?>
    </head>
    
    <body>
        <div class="container">
            <div class="message">
                <img src="<?php echo $config_domain; ?>/api/assets/img/play.png" class="start" />
            </div>
            <div id="player"></div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/disable-devtool/disable-devtool.min.js" disable-devtool-auto url='<?php echo $config_domain; ?>'></script>
        <script src="<?php echo $config_domain; ?>/api/assets/js/script.js?ver=<?php echo strtotime('now'); ?>"></script>
    </body>
</html>

