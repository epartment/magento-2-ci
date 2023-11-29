# Migrating to the New Docker Image in GitLab CI

## Introduction

This document provides a guide for migrating from the `epartment/magento-2-ci:<tag>` Docker image to our newly updated Docker image in GitLab CI pipelines. The new image comes with enhanced features and requires specifying the Node.js version in the tag to include Node.js.

## Prerequisites

- Familiarity with GitLab CI/CD.
- Current usage of `epartment/magento-2-ci:<tag>` in your `.gitlab-ci.yml`.

## Important Update

The new Docker image requires you to specify the Node.js version in the tag. If you don't specify a Node.js version, Node.js will not be included in the image.

## Migration Steps

### 1. Review Current Configuration

Determine your current PHP and Node.js versions in use with `epartment/magento-2-ci:<tag>`.

### 2. Choose an Appropriate New Image Tag

Select a tag from the new image options that matches your PHP and Node.js versions:
- For PHP 7.4 with Node.js 10, use `epartment/gitlab-ci:7.4-node10`.
- For PHP 8.1 with Node.js 14, use `epartment/gitlab-ci:8.1-node14`.

### 3. Update Your `.gitlab-ci.yml` File

Modify your file to use the new Docker image. Here are updated examples:

#### Example for PHP 7.4 with Node.js 10

```yaml
image: epartment/gitlab-ci:7.4-node10

stages:
  - build

build:
  stage: build
  script:
    - composer install
    - yarn install
    - grunt watch
```

#### Example for PHP 8.1 with Node.js 14

```yaml
image: epartment/gitlab-ci:8.1-node14

stages:
  - build

build:
  stage: build
  script:
    - composer install
    - npm install
    - gulp build
```

### 4. Adjust Build Scripts

New versions of tools (Composer, Node.js) might be in the new image. Update your scripts to be compatible with these versions.

### 5. Test the New Configuration

Run your CI pipeline to validate the changes. Pay attention to potential issues arising from the Docker image switch.

## Conclusion

With this migration, your CI pipeline will be updated to a more capable Docker image. Remember, specifying the Node.js version in the tag is crucial for including Node.js in the image.