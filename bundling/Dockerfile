# GitLab CI image for Magento 2 JavaScript bundling and critical-CSS generation.
#
# Purpose-built for the "js" and "critical-css" jobs of the epartment/deployer
# pipeline. Those jobs crawl a running shop with Puppeteer, build a theme with
# gulp, and rsync the results to the deploy target. They do NOT run Composer,
# Magento, or any PHP application code, so this image ships none of that: no
# Composer, no PHP, no PhantomJS, no Python. That takes it to about 330 MiB
# compressed against 640 MiB for the general-purpose
# epartment/gitlab-ci:<php>-node<node> image it replaces for these jobs.
#
# Debian, not Alpine, on purpose: "puppeteer browsers install" downloads a
# glibc build of Chrome for Testing, which cannot run against musl.
#
# What the jobs need, and why each piece is here:
#   node, npm         theme builds and the bundling crawler
#   puppeteer (global) the crawler does a bare require('puppeteer'), with no
#                     npm install of its own to pull it in
#   chrome            installed at build time into PUPPETEER_CACHE_DIR, with
#                     the shared libraries it needs to actually start
#   gulp              the critical-CSS job runs the theme's gulp tasks
#   rsync, ssh        moving artefacts to and from the deploy target
#   git               the js job clones epartment/javascript-bundling by hand
#
# No PHP and no Composer: the one thing PHP used to do here, reading the
# project's composer.lock, is done in node, so this image never has to match a
# Magento project's PHP version.

ARG NODE_VERSION
ARG OS_RELEASE

FROM node:${NODE_VERSION}-${OS_RELEASE:-bookworm}-slim

ENV DEBIAN_FRONTEND=noninteractive

# Runtime libraries required by Chrome for Testing, plus the handful of CLI
# tools the two jobs shell out to. Distribution Chromium is deliberately not
# installed; the browser comes from Puppeteer below, so its version always
# matches the Puppeteer that drives it.
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
      ca-certificates \
      curl \
      git \
      jq \
      openssh-client \
      rsync \
      unzip \
      fonts-liberation \
      libasound2 \
      libatk-bridge2.0-0 \
      libatk1.0-0 \
      libc6 \
      libcairo2 \
      libcups2 \
      libdbus-1-3 \
      libdrm2 \
      libexpat1 \
      libgbm1 \
      libglib2.0-0 \
      libgtk-3-0 \
      libnspr4 \
      libnss3 \
      libpango-1.0-0 \
      libstdc++6 \
      libx11-6 \
      libxcb1 \
      libxcomposite1 \
      libxdamage1 \
      libxext6 \
      libxfixes3 \
      libxkbcommon0 \
      libxrandr2 \
      xdg-utils; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# Global build tooling. Puppeteer is global because the bundling crawler is run
# straight from a git clone with no npm install of its own.
#
# The browser is fetched as its own step rather than as a side effect of the
# npm install, so that a failure to obtain it fails the build loudly. Note that
# @puppeteer/browsers shells out to unzip to extract Chrome; without it the
# install leaves an empty version folder behind and every later launch, and
# every later install, fails on that half-written directory.
#
# PUPPETEER_VERSION is an npm version or dist-tag, set per Node version in
# constants.php (PUPPETEER_VERSIONS). Unpinned, npm installs whatever is newest
# on the day of the build, which on Node 16 is a Puppeteer that cannot run
# there. Like the other build args it has no default.
#
# @puppeteer/browsers 1.x (Puppeteer 21, used on Node 16) sends its downloads
# with keep-alive and leaves a TLS socket open afterwards; on Node 16 the
# process then never exits, which hangs this build and the critical-CSS job's
# own "puppeteer browsers install". Keep-alive is switched off in that version
# only, and the grep fails the build if the patched line ever changes shape.
# Puppeteer 21 launches old headless mode from chrome-headless-shell, so that
# browser is installed as well.
ARG PUPPETEER_VERSION
ENV PUPPETEER_CACHE_DIR=/opt/puppeteer
RUN set -eux; \
    PUPPETEER_SKIP_DOWNLOAD=true PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
      npm install -g gulp-cli "puppeteer@${PUPPETEER_VERSION}"; \
    for pkg in $(find /usr/local/lib/node_modules -path '*/@puppeteer/browsers/package.json'); do \
      case "$(node -p "require('$pkg').version")" in \
        1.*) for f in "$(dirname "$pkg")"/lib/*/httpUtil.js; do \
               sed -i 's/keepAlive = true/keepAlive = false/' "$f"; \
               grep -q 'keepAlive = false' "$f"; \
             done ;; \
      esac; \
    done; \
    rm -rf /opt/puppeteer; \
    puppeteer browsers install chrome; \
    case "$(node -p "require('/usr/local/lib/node_modules/puppeteer/package.json').version")" in \
      21.*) puppeteer browsers install chrome-headless-shell ;; \
    esac; \
    npm cache clean --force; \
    chmod -R a+rX /opt/puppeteer

ENV NODE_PATH="/usr/local/lib/node_modules"

# Fail the build here rather than in a client's pipeline.
RUN set -eux; \
    node --version; \
    npm --version; \
    gulp --version; \
    rsync --version | head -n 1; \
    ssh -V; \
    git --version; \
    jq --version; \
    command -v puppeteer > /dev/null || { echo "missing binary: puppeteer" >&2; exit 1; }; \
    node -e "const p=require('puppeteer'); p.launch({args:['--no-sandbox']}).then(async b => { console.log('chrome', await b.version()); await b.close(); }).catch(e => { console.error(e.message); process.exit(1); })"
