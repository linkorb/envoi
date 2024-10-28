#!/usr/bin/env bash
# Managed by https://github.com/linkorb/repo-ansible. Manual changes will be overwritten.

# pre-push composer-unused hook disabled. See Card #8980
if [ -f .git/hooks/pre-push ]; then
  diff .devcontainer/git/hooks/pre-push .git/hooks/pre-push
  if [ $? -eq 0 ]; then
    rm .git/hooks/pre-push
  fi
fi
