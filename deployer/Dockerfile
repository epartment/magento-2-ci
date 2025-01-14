ARG OS_RELEASE
ARG NODE_VERSION
ARG DEPLOYER_VERSION

FROM node:${NODE_VERSION}-${OS_RELEASE:-bullseye} AS node
RUN npm install -g grunt-cli gulp puppeteer requirejs terser penthouse
RUN mv /opt/yarn* /opt/yarn

FROM deployphp/deployer:${DEPLOYER_VERSION}

# Copy Node to Deployer
COPY --from=node /usr/local/include/node /usr/local/include/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /opt /opt
COPY --from=node /usr/local/bin/node /usr/local/bin/node

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
    && ln -s /opt/yarn/bin/yarnpkg /usr/local/bin/yarnpkg \
    && ln -s /usr/local/lib/node_modules/puppeteer/lib/esm/puppeteer/node/cli.js /usr/local/bin/puppeteer

ENV PATH="${PATH}:/usr/local/lib/node_modules/bin"
ENV NODE_PATH="/usr/local/lib/node_modules"
