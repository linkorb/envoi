# Managed by https://github.com/linkorb/repo-ansible. Manual changes will be overwritten.

FROM ghcr.io/linkorb/php-docker-base:php8-review
EXPOSE 80

USER root

ENV APP_ENV=dev

ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=$USER_UID

ENV APACHE_RUN_USER    $USERNAME
ENV APACHE_RUN_GROUP   $USERNAME
ENV APACHE_LOCK_DIR    /var/lock/apache2
ENV APACHE_LOG_DIR     /var/log/apache2
ENV APACHE_PID_FILE    /var/run/apache2/apache2.pid

# Create a non-root user with the specified UID and GID
RUN groupadd --gid $USER_GID $USERNAME \
    && useradd --uid $USER_UID --gid $USER_GID -m $USERNAME \
    && apt-get update \
    && apt-get install -y sudo \
    && echo $USERNAME ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/$USERNAME \
    && chmod 0440 /etc/sudoers.d/$USERNAME \
    && sudo chsh -s /bin/bash vscode

RUN chown vscode:vscode -R /var/log/apache2

RUN echo "xdebug.mode=off" | tee '/usr/local/etc/php/conf.d/xdebug.ini'

WORKDIR /app
