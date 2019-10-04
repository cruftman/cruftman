ARG CRUFT_IMG
FROM ${CRUFT_IMG}
MAINTAINER Paweł Tomulik <ptomulik@meil.pw.edu.pl>

ARG CRUFT_USER='cruftman'
ARG CRUFT_GROUP='cruftman'
ARG CRUFT_UID=6789
ARG CRUFT_GID=6789
ARG CRUFT_HOME=/home/${CRUFT_USER}
ARG CRUFT_CODE=/code
ARG CRUFT_DOTENV_FILE=.env.docker
ARG CRUFT_BEHAT_PARAMS='{"extensions":{"Behat\\MinkExtension":{"base_url":"http://http-service"}}}'

ENV CRUFT_USER=$CRUFT_USER \
    CRUFT_GROUP=$CRUFT_GROUP \
    CRUFT_UID=$CRUFT_UID \
    CRUFT_GID=$CRUFT_GID \
    CRUFT_HOME=$CRUFT_HOME \
    CRUFT_CODE=$CRUFT_CODE \
    CRUFT_DOTENV_FILE=$CRUFT_DOTENV_FILE \
    BEHAT_PARAMS=$CRUFT_BEHAT_PARAMS

RUN apt-get update && \
    apt-get install -y --no-install-recommends mariadb-client && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    if [ -f /etc/alpine-release ]; then \
      addgroup -g $CRUFT_GID $CRUFT_GROUP && \
      adduser -h $CRUFT_HOME -G $CRUFT_GROUP -u $CRUFT_UID -D $CRUFT_USER; \
    else \
      groupadd -g $CRUFT_GID $CRUFT_GROUP && \
      useradd -m -d $CRUFT_HOME -g $CRUFT_GROUP --uid $CRUFT_UID $CRUFT_USER; \
    fi; \
    mkdir -p $CRUFT_CODE && chown $CRUFT_USER:$CRUFT_GROUP $CRUFT_CODE && \
    if [ -e "/tmp/cc" ]; then chown -R $CRUFT_USER:$CRUFT_GROUP "/tmp/cc"; fi

USER $CRUFT_USER

VOLUME $CRUFT_CODE

WORKDIR $CRUFT_CODE

# vim: set ft=dockerfile:
