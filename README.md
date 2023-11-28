# Using the Docker Image in GitLab CI

This Docker image is designed to be used in GitLab CI pipelines. It contains the necessary tools and dependencies for building and testing your project.

This Docker image contains:

- PHP
- Composer
- Node.js
- NPM
- Yarn
- Grunt
- Gulp
- PhantomJS

It has the following PHP extensions installed:

- APCu
- AMQP
- BCMath
- Calendar
- Exif
- GD
- Intl
- Imagick
- IMAP
- MySQLi
- PCNTL
- PDO MySQL
- Redis
- SOAP
- Sockets
- Sodium
- XSL
- ZIP
- Mcrypt (for PHP < 8.2)


To use this image, you can specify the image tag in your  `.gitlab-ci.yml`  file. Here's an example:

### Composer 2
image: `epartment/gitlab-ci:<tag>`
Replace  `<tag>`  with the desired version of the image. The available tags are:

-  `latest` : This tag includes latest PHP version without Node.js
-  `latest-nodelatest` : This tag includes latest PHP version and latest Node.js version available
-  `7.4` : This tag includes PHP 7.4 without Node.js
-  `7.4-node10` : This tag includes PHP 7.4 + Node.js version 10.x
-  `8.1-node14` : This tag includes PHP 8.1 + Node.js version 14.x

### Composer 1
image: `epartment/gitlab-ci-composer1:<tag>`
Replace  `<tag>`  with the desired version of the image. The available tags are:

-  `latest` : This tag includes latest PHP version without Node.js
-  `latest-nodelatest` : This tag includes latest PHP version and latest Node.js version available
-  `7.4` : This tag includes PHP 7.4 without Node.js
-  `7.4-node10` : This tag includes PHP 7.4 + Node.js version 10.x
-  `8.1-node14` : This tag includes PHP 8.1 + Node.js version 14.x

Choose the appropriate tag based on your project's requirements. If you're not sure which tag to use, you can start with  `latest`  as it includes the latest versions of Node.js and Composer.

In your GitLab CI pipeline, you can then run commands using the tools available in the image. For example:
```yaml
stages:
  - build

build:
  stage: build
  script:
    - composer install
    - yarn install
    - grunt watch
```
This example shows a simple build stage that runs Composer install, Yarn install, and Grunt watch commands.

Remember to adjust the commands according to your project's needs.

Please note that this image is specifically tailored for GitLab CI usage and may not work as expected outside of the GitLab CI environment.

I hope this helps! Let me know if you have any further questions.