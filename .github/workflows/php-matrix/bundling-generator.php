<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'constants.php');

// Two matrices: "build_matrix" has one row per Node version and platform, each
// built on its own native runner; "matrix" has one row per Node version, for
// the job that merges the per-platform images into one tag.
$matrix = [];
$buildMatrix = [];

foreach (BUNDLING_NODE_VERSIONS as $nodeVersion) {
    $platforms = array_key_exists($nodeVersion, BUNDLING_NODE_PLATFORMS) ? BUNDLING_NODE_PLATFORMS[$nodeVersion] : BUNDLING_DEFAULT_PLATFORMS;
    $matrix[] = [
        'node_version' => $nodeVersion,
        'latest' => $nodeVersion === BUNDLING_NODE_LATEST,
        'platform_count' => count($platforms),
    ];

    foreach ($platforms as $platform) {
        $buildMatrix[] = [
            'node_version' => $nodeVersion,
            'node_os_release' => BUNDLING_OS_RELEASE,
            'puppeteer_version' => PUPPETEER_VERSIONS[$nodeVersion],
            'platform' => $platform,
            'platform_pair' => str_replace('/', '-', $platform),
            'runner' => BUNDLING_RUNNERS[$platform],
        ];
    }
}

echo 'matrix=' . json_encode(['include' => $matrix]) . PHP_EOL;
echo 'build_matrix=' . json_encode(['include' => $buildMatrix]) . PHP_EOL;
