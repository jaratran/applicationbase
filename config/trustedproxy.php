<?php

return [
    // Comma-separated proxy IPs or CIDR ranges. No proxy is trusted by default.
    'proxies' => array_values(array_filter(array_map(
        trim(...),
        explode(',', (string) env('TRUSTED_PROXIES', ''))
    ))),
];
