<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'constants.php');

$matrix = [];

foreach (DEPLOYER_VERSIONS as $deployerVersion) {
    foreach (NODE_VERSIONS as $nodeVersion) {
        // The fallback is a bare tag suffix: it is appended to "node:<version>-"
        // in the Dockerfile, so it must not repeat the version.
        $nodeOsRelease = array_key_exists($nodeVersion, NODE_VERSIONS_OS_RELEASE_ALPINE) ? NODE_VERSIONS_OS_RELEASE_ALPINE[$nodeVersion] : 'alpine3.20';
        $matrix[] = [
            'deployer_version' => $deployerVersion,
            'node_version' => $nodeVersion,
            'node_os_release' => $nodeOsRelease,
            'latest' => $deployerVersion === DEPLOYER_LATEST && $nodeVersion === DEPLOYER_NODE_LATEST,
        ];
    }
}

echo 'matrix=' . json_encode(['include' => $matrix]);