<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "9200",
  CURLOPT_URL => "http://localhost:9200/ordar",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_POSTFIELDS => "{\n\t\"settings\": {\n    \"analysis\": {\n      \"normalizer\": {\n        \"myLowercase\": {\n          \"type\": \"custom\",\n          \"filter\": [ \"uppercase\" ]\n        }\n      }\n    }\n  },\n  \"mappings\": {\n    \"_default_\": { \n\n     \"properties\":{\n      \"INTRO.FILE_CREATOR.NAME\": { \n      \"type\":     \"keyword\",\n      \"index\": \"not_analyzed\",\n      \"normalizer\": \"myLowercase\",\n       \"ignore_above\": 256\n\t\t},\n\t \"INTRO.SCIENTIFIC_FIELD.NAME\": { \n      \"type\":     \"keyword\",\n      \"index\": \"not_analyzed\",\n      \"normalizer\": \"myLowercase\",\n       \"ignore_above\": 256\n\t\t\t},\n\t\"INTRO.CREATION_DATE\": { \n      \"type\":     \"date\",\n      \"format\": \"yyyy-MM-dd\"\n\t\t},\n\t\"INTRO.PROJECT_NAME\": { \n      \"type\":     \"keyword\",\n      \"index\": \"not_analyzed\",\n      \"normalizer\": \"myLowercase\",\n       \"ignore_above\": 256\n\t\t\t},\n\t\"INTRO.LANGUAGE\": { \n      \"type\":     \"keyword\",\n      \"index\": \"not_analyzed\",\n      \"normalizer\": \"myLowercase\",\n       \"ignore_above\": 256\n\t\t\t},\n\t\"INTRO.SAMPLE_KIND.NAME\": { \n      \"type\":     \"keyword\",\n      \"index\": \"not_analyzed\",\n      \"normalizer\": \"myLowercase\",\n       \"ignore_above\": 256\n\t\t\t},\n\t\"INTRO.KEYWORDS.NAME\": { \n      \"type\":     \"keyword\",\n      \"index\": \"not_analyzed\",\n      \"normalizer\": \"myLowercase\",\n       \"ignore_above\": 256\n\t\t\t},\n\t\"INTRO.ACCESS_RIGHT\": { \n      \"type\":     \"keyword\",\n      \"index\": \"not_analyzed\"\n\t\t\t},\n\t\"DATA.FILES.FILETYPE\": { \n      \"type\":     \"keyword\",\n      \"index\": \"not_analyzed\",\n      \"normalizer\": \"myLowercase\",\n       \"ignore_above\": 256\n\t\t\t}\n\t\t}\n    }\n  }\n}",

));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}