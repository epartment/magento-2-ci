# GitLab CI image for Deployer-driven Magento 2 deployments.
#
# Contains the Deployer PHAR (from deployphp/deployer) plus the Node build
# toolchain used for Magento theme builds: node, npm, npx, yarn, gulp, grunt,
# requirejs and terser.
#
# Deliberately NOT included: Chromium, Puppeteer and penthouse. Browser
# automation belongs in the epartment/gitlab-ci:bundling-node<node> image, which
# is what the JS-bundling and critical-CSS jobs run on. Dropping it, and copying
# only Node's own trees instead of the whole of /usr/lib, takes the compressed
# image from 749 MiB to about 100 MiB. It also keeps the image running as root,
# which the deploy jobs need in order to write ~/.ssh in their before_script.

ARG OS_RELEASE
ARG NODE_VERSION
ARG DEPLOYER_VERSION

FROM node:${NODE_VERSION}-${OS_RELEASE:-alpine3.20} AS node
RUN npm install -g grunt-cli gulp requirejs terser
RUN mv /opt/yarn* /opt/yarn

FROM deployphp/deployer:${DEPLOYER_VERSION}

# Node's prebuilt binary links against libstdc++, which the PHP Alpine base
# image does not ship. Install it from apk rather than copying the node image's
# /usr/lib into this image: that would overwrite the PHP base image's own
# system libraries with a different Alpine release's.
RUN apk add --no-cache libstdc++

# Copy only the trees Node actually owns.
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/include/node /usr/local/include/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /opt /opt

# The copied trees carry no bin symlinks of their own, so recreate them. Same
# set as the node/Dockerfile used by the PHP images, minus puppeteer.
RUN ln -s /usr/local/bin/node /usr/local/bin/nodejs \
    && ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && ln -s /usr/local/lib/node_modules/requirejs/bin/r.js /usr/local/bin/requirejs \
    && ln -s /usr/local/lib/node_modules/requirejs/bin/r.js /usr/local/bin/r.js \
    && ln -s /usr/local/lib/node_modules/terser/bin/terser /usr/local/bin/terser \
    && ln -s /usr/local/lib/node_modules/terser/bin/uglifyjs /usr/local/bin/uglifyjs \
    && ln -s /usr/local/lib/node_modules/gulp/bin/gulp.js /usr/local/bin/gulp \
    && ln -s /usr/local/lib/node_modules/grunt-cli/bin/grunt /usr/local/bin/grunt \
    && ln -s /opt/yarn/bin/yarn /usr/local/bin/yarn \
    && ln -s /opt/yarn/bin/yarnpkg /usr/local/bin/yarnpkg

ENV NODE_PATH="/usr/local/lib/node_modules"

# Upstream now installs the PHAR as /bin/dep. Images built before 2025-02 had it
# at /bin/deployer.phar, and pipelines still invoke it by that path, so keep the
# old name working as well. This applies to the v7 tag too: the rename landed
# upstream after the last epartment deployer image was built.
RUN if [ -f /bin/dep ] && [ ! -e /bin/deployer.phar ]; then \
      ln -s /bin/dep /bin/deployer.phar; \
    fi

# Fail the build here rather than in a client's pipeline if a copy or a symlink
# is wrong. grunt and r.js are only checked for presence: both exit non-zero
# when run outside a project.
RUN node --version \
    && npm --version \
    && yarn --version \
    && for bin in nodejs npx gulp grunt requirejs r.js terser uglifyjs; do \
         command -v "$bin" > /dev/null || { echo "missing binary: $bin" >&2; exit 1; }; \
       done \
    && dep --version \
    && php /bin/deployer.phar --version
