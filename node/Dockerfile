ARG ENV_SOURCE_IMAGE
ARG PHP_VERSION
ARG OS_RELEASE
ARG NODE_VERSION

FROM node:${NODE_VERSION}-${OS_RELEASE:-bullseye} AS node
# PUPPETEER_VERSION is an npm version or dist-tag, set per Node version in
# constants.php (PUPPETEER_VERSIONS). Unpinned, npm installs whatever is newest
# on the day of the build, which on Node 16, 19 and 21 is a Puppeteer that
# cannot run there. Like the other build args it has no default.
ARG PUPPETEER_VERSION
RUN PUPPETEER_SKIP_DOWNLOAD=true PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    npm install -g grunt-cli gulp "puppeteer@${PUPPETEER_VERSION}" requirejs terser
RUN mv /opt/yarn* /opt/yarn

FROM ${ENV_SOURCE_IMAGE}:${PHP_VERSION}
USER root

# Copy Node to PHP
COPY --from=node /usr/local/include/node /usr/local/include/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /opt /opt
COPY --from=node /usr/local/bin/node /usr/local/bin/node

# Copy PhantomJS
COPY --from=rollupdev/phantomjs:latest /build/phantomjs/bin/phantomjs /usr/local/bin/phantomjs

RUN ln -s /usr/local/bin/node /usr/local/bin/nodejs \
    && ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/requirejs/bin/r.js /usr/local/bin/requirejs \
    && ln -s /usr/local/lib/node_modules/requirejs/bin/r.js /usr/local/bin/r.js \
    && ln -s /usr/local/lib/node_modules/terser/bin/terser /usr/local/bin/terser \
    && ln -s /usr/local/lib/node_modules/terser/bin/uglifyjs /usr/local/bin/uglifyjs \
    && ln -s /usr/local/lib/node_modules/gulp/bin/gulp.js /usr/local/bin/gulp \
    && ln -s /usr/local/lib/node_modules/grunt-cli/bin/grunt /usr/local/bin/grunt \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && ln -s /opt/yarn/bin/yarn /usr/local/bin/yarn \
    && ln -s /opt/yarn/bin/yarnpkg /usr/local/bin/yarnpkg

ENV PATH="${PATH}:/usr/local/lib/node_modules/bin"
ENV NODE_PATH="/usr/local/lib/node_modules"

# Reinstall Puppeteer here so npm links the puppeteer binary into /usr/local/bin,
# then fetch the browser as its own step so a failure fails the build loudly.
#
# @puppeteer/browsers 1.x (Puppeteer 21, used on Node 16) sends its downloads
# with keep-alive and leaves a TLS socket open afterwards; on Node 16 the
# process then never exits, which hangs this build and every "puppeteer
# browsers install" a pipeline runs. Keep-alive is switched off in that version
# only, and the grep fails the build if the patched line ever changes shape.
# Puppeteer 21 launches old headless mode from chrome-headless-shell, so that
# browser is installed as well.
ARG PUPPETEER_VERSION
RUN set -eux; \
    PUPPETEER_SKIP_DOWNLOAD=true PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
      npm -g install "puppeteer@${PUPPETEER_VERSION}"; \
    for pkg in $(find /usr/local/lib/node_modules -path '*/@puppeteer/browsers/package.json'); do \
      case "$(node -p "require('$pkg').version")" in \
        1.*) for f in "$(dirname "$pkg")"/lib/*/httpUtil.js; do \
               sed -i 's/keepAlive = true/keepAlive = false/' "$f"; \
               grep -q 'keepAlive = false' "$f"; \
             done ;; \
      esac; \
    done; \
    puppeteer browsers install chrome; \
    case "$(node -p "require('/usr/local/lib/node_modules/puppeteer/package.json').version")" in \
      21.*) puppeteer browsers install chrome-headless-shell ;; \
    esac

# Fail the build here rather than in a client's pipeline: the crawler does a bare
# require('puppeteer'), and the critical-CSS job runs the puppeteer binary.
RUN set -eux; \
    node --version; \
    puppeteer --version; \
    node -e "const p = require('puppeteer'); Promise.resolve(p.executablePath()).then(e => { if (!require('fs').existsSync(e)) { console.error('missing browser: ' + e); process.exit(1); } console.log('browser', e); })"
