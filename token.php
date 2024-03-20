<?php

ini_set('error_reporting', E_STRICT);
require_once("./vendor/autoload.php");
use Wepesi\App\JWT;
$tken = new JWT();
$token = $_GET['token'];
$dec = $tken->decode($token);
//var_dump($dec);
//echo $dec['data'];
$video=$dec['data'];
//$video= hex2bin($_GET['id']);
//$video=$_GET['id'];
$lol="";
  for ($g=0;$g<strlen($video);$g++) {
   $lol .=" ";
  }

$filelink="https://membed.net/streaming.php?id=".$video.$lol;
if (preg_match("/vidnext\.net|vidnode\.net|vidembed\.(net|cc|io)|\/vidcloud9\.|membed\.net/",$filelink)) {
  require_once("./vendor/class/aes.php");
  $t1=explode("&",$filelink);
  $rest=$t1[1];
  $x=parse_url($filelink);
  $host=$x['host'];
  parse_str($x['query'],$y);
  $id=$y['id'];
  $id1=$id;
  unset($y['id']);
  $q=http_build_query($y);
  // see https://vidembed.io/js/player2021.min.js?v=7.5
  //$key = '25746538592938496764662879833288';
  //$iv="9225679083961858";   // random
  $key="25742532592138496744665879883281";     // new 10.03.2022
  $iv="9225679083961858";
  $aes = new Aes($key, 'CBC', $iv);
  $out="";
  for ($k=0;$k<strlen($id);$k++) {
   $out .="%08";
  }
  $id = $id.$out;
  $e=urldecode($id);
  $y = $aes->encrypt($e);
  $enc=base64_encode($y);
  //$l="https://membed.net/encrypt-ajax.php?id=".$enc."&".$rest."&c=aaaaaaaa&refer=none&time=52564103982551631204";
  $ciao= str_replace(" ", "",$id1);
  $l="https://membed.net/encrypt-ajax.php?id=".$enc."&c=aaaaaaaa&refer=none&alias=".$ciao;
  //echo $l;
  $head=array(
   'Accept: */*',
   'cache-control: no-cache',
   'origin: https://'.$host.'/',
   'pragma: no-cache',
   'Referer: https://'.$host.'/',
   'X-Requested-With: XMLHttpRequest',
   'sec-fetch-dest: empty',
   'sec-fetch-mode: cors',
   'sec-fetch-site: cross-site',
   'sec-gpc: 1',
   'user-agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.114 Safari/537.36',
   );

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.114 Safari/537.36');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  
  $r=json_decode($h2,1);
  $x=$r['data'];
  $x=base64_decode($x);
  $y = $aes->decrypt($x);
  $y = preg_replace('/[[:^print:]]/', '', $y);

  $r=json_decode($y,1);

  if (isset($r['source'][0]['file'])) {
   $c=count($r['source'])-1;
   if (preg_match("/Auto/i",$r['source'][$c]['label']) && $c>1) $c=$c-1;
   $link= $r['source'][$c]['file'];
  }

  if (isset($r['track']['tracks']['file']))
   $srt=$r['track']['tracks']['file'];
  elseif (isset($r['track']['tracks'][0]['file']))
   $srt=$r['track']['tracks'][0]['file'];
}

if(strtolower(end(explode(".",$link))) =="mp4") {
$type = "video/mp4";
$androidhls = false;
$hlshtml = false;
} else {
$type = "hls";
$androidhls = true;
$hlshtml = true;
}

 if (empty($link)) {
       $status = 401;
       $player = "https://cdn.jwplayer.com/videos/bug7ziFx-JiQoCWml.mp4";
    } else {
       $status = 200;
       $player = $link;
    }
 if (empty($srt)) {
       $subtitles = "";
    } else {
       $subtitles = $srt;
    }
   
$tracks=[

        "kind"=>"captions",
        "file"=>$subtitles,
        "label"=>"English",
        "deault"=>false,

    ];
    
$subs = [$tracks];
$myjson->status = $status;
$myjson->file = base64_encode($player);
$myjson->type = $type;
$myjson->androidhls = $androidhls;
$myjson->hlshtml = $hlshtml;
$myjson->tracks = $subs;
$newjson = json_encode($myjson);
header( 'Content-Type: application/json' );
echo $newjson;
?>



