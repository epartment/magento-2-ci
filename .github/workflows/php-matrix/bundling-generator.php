<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'constants.php');

$matrix = [];

foreach (BUNDLING_NODE_VERSIONS as $nodeVersion) {
    // The bundling image is Debian-based, so it uses the same OS release map as
    // the PHP + Node images. The fallback is a bare tag suffix: it is inserted
    // into "node:<version>-<suffix>-slim", so it must not repeat the version.
    $nodeOsRelease = array_key_exists($nodeVersion, NODE_VERSIONS_OS_RELEASE) ? NODE_VERSIONS_OS_RELEASE[$nodeVersion] : 'bookworm';
    $matrix[] = [
        'node_version' => $nodeVersion,
        'node_os_release' => $nodeOsRelease,
        'latest' => $nodeVersion === BUNDLING_NODE_LATEST,
    ];
}

echo 'matrix=' . json_encode(['include' => $matrix]);
