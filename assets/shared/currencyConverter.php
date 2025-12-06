<?php
if (!function_exists('convertCurrency')) {

    // Cache the rate so the API is only called once per request
    $GLOBALS['cached_rates'] = [];

    function convertCurrency($amount, $from, $to)
    {
        // No conversion needed
        if ($from === $to) return (float)$amount;

        // Check cache
        $cache_key = "{$from}_{$to}";
        if (isset($GLOBALS['cached_rates'][$cache_key])) {
            return (float)$amount * $GLOBALS['cached_rates'][$cache_key];
        }

        // Request correct Frankfurter format
        $url = "https://api.frankfurter.app/latest?from={$from}&to={$to}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) return null;

        $data = json_decode($response, true);

        // Forward conversion available
        if (isset($data['rates'][$to])) {
            $rate = (float)$data['rates'][$to];
            $GLOBALS['cached_rates'][$cache_key] = $rate;
            return $amount * $rate;
        }

        // Try reverse conversion (e.g., PHP not supported)
        $reverseUrl = "https://api.frankfurter.app/latest?from={$to}&to={$from}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $reverseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) return null;

        $data = json_decode($response, true);

        if (!isset($data['rates'][$from])) return null;

        // Reverse conversion: amount * (1 / reverseRate)
        $reverseRate = (float)$data['rates'][$from];
        $rate = 1 / $reverseRate;

        $GLOBALS['cached_rates'][$cache_key] = $rate;

        return $amount * $rate;
    }
}
?>
