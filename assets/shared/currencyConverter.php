<?php
function convertCurrency($amount, $from, $to) {
    if ($from === $to) return $amount; 

    $url = "https://api.exchangerate.host/convert?from=$from&to=$to&amount=$amount";
    $json = file_get_contents($url);

    if (!$json) return $amount;

    $data = json_decode($json, true);

    return $data['result'] ?? $amount;
}
?>
