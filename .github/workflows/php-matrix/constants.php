<?php

const PHP_LATEST = '8.3';

/*
* Don't include older builds because these are already build and not going to be updated from official repo
* If there is something changed in our Dockerfile and you want to build all versions then define it as follows:
* const PHP_VERSIONS = ['7.1', '7.2', '7.3', '7.4', '8.0', '8.1', '8.2'];
*/
const PHP_VERSIONS = ['7.1', '7.2', '7.3', '7.4', '8.0', '8.1', '8.2', '8.3'];
const PHP_VERSIONS_OS_RELEASE = [
    '7.1' => 'buster',
    '7.2' => 'buster',
    '7.3' => 'bullseye',
    '7.4' => 'bullseye',
    '8.0' => 'bullseye',
    '8.1' => 'bookworm',
    '8.2' => 'bookworm',
    '8.3' => 'bookworm'
];
const NODE_LATEST = '21';
const NODE_VERSIONS = ['16', '18', '19', '20', '21', '22'];
const NODE_VERSIONS_OS_RELEASE = [
    '16' => 'bullseye',
    '18' => 'bullseye',
    '19' => 'bullseye',
    '20' => 'bookworm',
    '21' => 'bookworm',
    '22' => 'bookworm',
];

const NODE_VERSIONS_OS_RELEASE_ALPINE = [
    '16' => 'alpine',
    '18' => 'alpine3.20',
    '19' => 'alpine3.20',
    '20' => 'alpine3.20',
    '21' => 'alpine3.20',
    '22' => 'alpine3.20',
];

const EXPERIMENTAL_PHP_VERSIONS = [];
const NOT_STABLE_XDEBUG_PHP_VERSIONS = ['7.0', '7.1', '7.2', '7.3', '7.4'];

/*
* Deployer major versions and Node versions to build deployer images for. Each
* Deployer value must exist as a tag on docker.io/deployphp/deployer.
*
* Only the combinations a pipeline actually pulls are built: the epartment/deployer
* package references deployer-v8-node22 and nothing else. The other
* deployer-<v>-node<n> tags remain on Docker Hub but are frozen; add a version
* here only when a pipeline starts using it.
*/
const DEPLOYER_VERSIONS = ['v8'];
const DEPLOYER_LATEST = 'v8';
const DEPLOYER_NODE_VERSIONS = ['22'];

/*
* The deployer and bundling images track their own "latest" Node version, so
* that moving it does not also move the latest-nodelatest tag of the PHP
* images (that one follows NODE_LATEST).
*/
const DEPLOYER_NODE_LATEST = '22';

/*
* Node versions built for the bundling image. Puppeteer downloads a glibc
* build of Chrome, so this image is Debian-based.
*
* Every bundling image uses the same Debian release, independent of
* NODE_VERSIONS_OS_RELEASE: bullseye's security archive was purged after its
* LTS ended on 2026-08-31, so apt-get install fails there, and this image has
* no PHP that would tie it to an older release.
*/
const BUNDLING_NODE_VERSIONS = ['18', '20', '22'];
const BUNDLING_NODE_LATEST = '22';
const BUNDLING_OS_RELEASE = 'bookworm';

/*
* Platforms the bundling image is built for, each on a native runner. Chrome
* does not start within Puppeteer's launch timeout under QEMU emulation, so the
* in-build puppeteer.launch() smoke test fails on an emulated arm64 build.
*
* arm64 is built for Node 22 only. Puppeteer 25 requires Node >= 22.12, so Node
* 18 and 20 get Puppeteer 24, whose Chrome 148 download for linux arm64 is the
* x86-64 build and cannot start there.
*/
const BUNDLING_RUNNERS = [
    'linux/amd64' => 'ubuntu-latest',
    'linux/arm64' => 'ubuntu-24.04-arm',
];
const BUNDLING_DEFAULT_PLATFORMS = ['linux/amd64'];
const BUNDLING_NODE_PLATFORMS = [
    '22' => ['linux/amd64', 'linux/arm64'],
];