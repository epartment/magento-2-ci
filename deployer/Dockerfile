ARG OS_RELEASE
ARG NODE_VERSION
ARG DEPLOYER_VERSION

FROM node:${NODE_VERSION}-${OS_RELEASE:-alpine} AS node
RUN npm install -g grunt-cli gulp puppeteer requirejs terser penthouse
RUN mv /opt/yarn* /opt/yarn

FROM deployphp/deployer:${DEPLOYER_VERSION}

# Copy Node to Deployer
COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin

#RUN ln -s /usr/local/bin/node /usr/local/bin/nodejs \
#    && ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
#    && ln -s /usr/local/lib/node_modules/requirejs/bin/r.js /usr/local/bin/requirejs \
#    && ln -s /usr/local/lib/node_modules/requirejs/bin/r.js /usr/local/bin/r.js \
#    && ln -s /usr/local/lib/node_modules/terser/bin/terser /usr/local/bin/terser \
#    && ln -s /usr/local/lib/node_modules/terser/bin/uglifyjs /usr/local/bin/uglifyjs \
#    && ln -s /usr/local/lib/node_modules/gulp/bin/gulp.js /usr/local/bin/gulp \
#    && ln -s /usr/local/lib/node_modules/grunt-cli/bin/grunt /usr/local/bin/grunt \
#    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
#    && ln -s /opt/yarn/bin/yarn /usr/local/bin/yarn \
#    && ln -s /opt/yarn/bin/yarnpkg /usr/local/bin/yarnpkg \
#    && ln -s /usr/local/lib/node_modules/puppeteer/lib/esm/puppeteer/node/cli.js /usr/local/bin/puppeteer

ENV PATH="${PATH}:/usr/local/bin:/usr/local/lib/node_modules/bin"
ENV NODE_PATH="/usr/local/lib/node_modules"

#Puppeteer https://github.com/puppeteer/puppeteer/blob/main/docs/troubleshooting.md#running-on-alpine

# Installs Chromium (100) package.
RUN apk add --no-cache \
      chromium \
      nss \
      freetype \
      harfbuzz \
      ca-certificates \
      ttf-freefont \
      nodejs \
      yarn

# Tell Puppeteer to skip installing Chrome. We'll be using the installed package.
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

# Puppeteer v13.5.0 works with Chromium 100.
RUN yarn add puppeteer@13.5.0

# Add user so we don't need --no-sandbox.
RUN addgroup -S pptruser && adduser -S -G pptruser pptruser \
    && mkdir -p /home/pptruser/Downloads /app \
    && chown -R pptruser:pptruser /home/pptruser \
    && chown -R pptruser:pptruser /app

# Run everything after as non-privileged user.
USER pptruser