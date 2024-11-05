#!/usr/bin/env bash
# Managed by https://github.com/linkorb/repo-ansible. Manual changes will be overwritten.

# Workaround for recent Python versions which prevent global pip package installation without an explicit flag
# or removal of a certain file.
sudo rm /usr/lib/python3.*/EXTERNALLY-MANAGED || true

# This is a workaround on an issue with the devcontainer reported on Windows where followup git config commands would
# fail. #9067
git config --add safe.directory /app

git config commit.template .devcontainer/git/linkorb_commit.template



composer install


