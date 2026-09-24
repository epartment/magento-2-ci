# CLAUDE.md

Guidance for AI coding assistants working in this repository. Read `README.md` first — it documents
the images, the tags, the build graph and the current known issues. This file covers only what is
easy to get wrong here.

## What this repository is

Five Dockerfiles and three GitHub Actions workflows that publish the Docker images every Magento
GitLab CI pipeline at the agency pulls. There is no application code.

## The one rule that matters

**A merge to `master` publishes to Docker Hub immediately.** Every `build-push-action` in every
workflow is gated on `github.ref == 'refs/heads/master'`, and the tags it overwrites are the ones
live client pipelines pull on their next deploy. There is no staging registry and no approval step.

Consequences:

- Treat an edit to any Dockerfile as a change to every project's CI, and say so when proposing one.
- Work on a branch, but note that a branch push triggers no workflow at all: `push` and
  `pull_request` are both filtered to `master`. Build locally, then open a pull request against
  `master` — a PR build runs the workflows without publishing, and is the only automated check
  before a merge that publishes.
- Never change what an existing version tag means. `8.3-node22` is PHP 8.3 with Node 22, forever.
  Only the floating aliases (`latest`, `latest-nodelatest`, `deployer-latest-nodelatest`,
  `bundling-nodelatest`) may move.

## Verify by building, not by reading

Docker images fail in ways that read fine on the page. Every substantive change to a Dockerfile in
this repository must be built locally before it is proposed as finished:

```bash
docker build --platform linux/amd64 --build-arg NODE_VERSION=22 --build-arg OS_RELEASE=bookworm -t test:bundling bundling/
```

Three defects in the current round were invisible on inspection and only surfaced from a real build:
the Deployer PHAR having moved to `/bin/dep`; a global `npm install -g puppeteer` creating a browser
folder it never fills; and `bookworm-slim` shipping no `unzip`, which `@puppeteer/browsers` shells
out to. None of these produce a warning — they produce a broken image or a failed client pipeline.

`deployer/Dockerfile` and `bundling/Dockerfile` therefore end with a `RUN` that executes every tool
they claim to ship, including an actual `puppeteer.launch()`. Keep that step, and extend it whenever
you add a tool. It is the difference between a broken build here and a broken deploy at a client.

## Invariants to preserve

1. **`constants.php` is the only place a version is declared.** All five matrix generators read it.
   Adding a PHP or Node version means appending to the array and adding its OS release to the
   matching map — never hardcoding a version in a workflow or Dockerfile.

2. **The PHP images are a chain, not three independent builds.** `Dockerfile` →`node/Dockerfile` →
   `composer1/Dockerfile`, joined by the `ENV_SOURCE_IMAGE` build arg and by `needs:` in
   `imageci.yml`. A change to the root `Dockerfile` rebuilds and can break all three. It is the
   riskiest file here.

3. **The deployer image must run as root.** The Deployer pipeline's shared `before_script` writes to
   `~/.ssh` and runs `ssh-keyscan`. An earlier revision ended with `USER pptruser`, which is why the
   images built from it were never adopted. Do not reintroduce a `USER` directive.

4. **The deployer image must keep the `/bin/deployer.phar` symlink.** Upstream moved the PHAR to
   `/bin/dep` on the `v7` tag as well as `v8`. Client pipelines still invoke it by the old path.

5. **The bundling image must stay Debian.** `puppeteer browsers install` downloads a glibc build of
   Chrome for Testing, which cannot run against musl. Alpine would look smaller and fail at runtime.

6. **Build args deliberately have no defaults.** A missing `NODE_VERSION` then produces an invalid
   base image name and an immediate failure rather than a silently wrong image. BuildKit's
   `InvalidDefaultArgInFrom` warning is expected; do not "fix" it by adding defaults.

## Things that look like bugs but are not

- **`deployer/Dockerfile` and `bundling/Dockerfile` duplicating Node setup.** They have different
  base distributions for a real reason (invariant 5). Merging them would break one of the two.
- **Two Docker Hub credential secrets.** `imageci.yml` uses `DOCKERHUB_USERNAME`/`DOCKERHUB_TOKEN`;
  the deployer and bundling workflows use `DOCKER_USERNAME`/`DOCKER_PASSWORD`. Both pairs exist and
  both work. It is untidy rather than broken — see finding M3 in `README.md`.
- **The `latest` flag in a matrix row.** It does not build an extra image; it gates a second push of
  the same build under a floating alias.
- **`.trigger` containing nothing but a hash.** It is listed in every workflow's `paths:` filter, so
  changing it is how you force a rebuild when no Dockerfile changed.
- **The bundling image not installing distribution Chromium.** The browser comes from Puppeteer so
  that its version always matches the Puppeteer driving it.

## Working here

Render any matrix without touching Docker:

```bash
php .github/workflows/php-matrix/deployer-generator.php
```

After editing `constants.php`, check that you changed only the matrices you meant to — the PHP image
generators share those constants with the deployer and bundling ones:

```bash
php .github/workflows/php-matrix/node-generator.php
```

Inspect a published image without pulling it:

```bash
docker buildx imagetools inspect epartment/gitlab-ci:deployer-v7
```

## Branching

- Default branch is `master`, and merging to it publishes. Branch off it for everything.
- `feature/<slug>` for changes.
- **A feature-branch push triggers nothing.** All three workflows filter `push` and `pull_request`
  to `master`, so branch pushes run no CI at all. Build locally (see "Verify by building" above),
  then open a pull request against `master` — a PR build runs the workflows without publishing, and
  is the only automated pre-merge check there is.

## Documentation

`README.md` follows a fixed structure with a findings section graded High / Medium / Low. When a
finding is fixed, remove it rather than marking it done — git history is the record. Update the
README in the same change that alters the behaviour it describes, and keep the tag catalogue honest:
it is what other teams read to choose an image.

Everything committed here must be self-contained: repository-relative paths only, no absolute host
paths, and no references to any individual's local tooling. Colleagues read this repository without
an AI setup.
