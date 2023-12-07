#!/usr/bin/env bash
# Managed by https://github.com/linkorb/repo-ansible. Manual changes will be overwritten.

git config commit.template .devcontainer/git/linkorb_commit.template

cp .devcontainer/git/hooks/pre-push .git/hooks/pre-push
chmod +x .git/hooks/pre-push

composer config --global --auth http-basic.repo.packagist.com "$GITHUB_USER" "$PACKAGIST_TOKEN"

composer install

