<?php


//Funciones CURL-----------------------------
function curlClientfyCall($link, $request = 'GET', $payload = false) {
  $now = date("Y-m-d H:i:s");
  $curl = curl_init();
  $headers[] = 'Authorization: Token '.CLIENTIFY_API_KEY;
  curl_setopt($curl, CURLOPT_URL, CLIENTIFY_API_URL.$link);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
  curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
  curl_setopt($curl, CURLOPT_ENCODING, '');
  curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
  curl_setopt($curl, CURLOPT_TIMEOUT, 0);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  if (in_array($request, array("PUT", "POST", "DELETE"))) curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request);
  if ($payload) {
    curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
    $headers[] = 'Content-Type: application/json';
  }
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  $response = curl_exec($curl);
  $json = json_decode($response);
  $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

  curl_close($curl);
  if (in_array($httpcode, array(200, 201, 204))) {
    if(CLIENTIFY_LOG_API_CALLS) curlClientfyLog("logs", $link, $request, $httpcode, $payload );
    return $json;
  } else {
    if(CLIENTIFY_LOG_API_CALLS) curlClientfyLog("errors", $link, $request, $httpcode, $payload, json_encode($json));
    //throw new Exception($httpcode." - ".json_encode($json));
    return false;
  }
}

//GET
function curlClientfyCallGet($link) { return curlClientfyCall($link); }

//PUT
function curlClientfyCallPut($link, $payload) { return curlClientfyCall($link, "PUT", $payload); }

//POST
function curlClientfyCallPost($link, $payload) { return curlClientfyCall($link, "POST", $payload); }

//DELETE
function curlClientfyCallDelete($link) { return curlClientfyCall($link, "DELETE"); }

//Log system
function curlClientfyLog($file, $link, $request, $httpcode, $payload = "", $json = "") {
  $f = fopen(dirname(__FILE__)."/../logs/".$file.".txt", "a+");
  $line = date("Y-m-d H:i:s")."|".$link."|".$request."|".$httpcode;
  if($payload != '') $line .= "|".$payload;
  if($json != '') $line .= "|".$json;
  $line .= "\n";
  fwrite($f, $line);
  fclose($f);
}
