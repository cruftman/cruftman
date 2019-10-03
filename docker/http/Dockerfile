ARG CRUFT_IMG
FROM ${CRUFT_IMG}
MAINTAINER Paweł Tomulik <ptomulik@meil.pw.edu.pl>

ARG CRUFT_USER='cruftman'
ARG CRUFT_GROUP='cruftman'
ARG CRUFT_UID=6789
ARG CRUFT_GID=6789
ARG CRUFT_HOME=/home/${CRUFT_USER}
ARG CRUFT_CODE=/code

ENV CRUFT_USER=$CRUFT_USER \
    CRUFT_GROUP=$CRUFT_GROUP \
    CRUFT_UID=$CRUFT_UID \
    CRUFT_GID=$CRUFT_GID \
    CRUFT_HOME=$CRUFT_HOME \
    CRUFT_CODE=$CRUFT_CODE \
    APACHE_RUN_USER=$CRUFT_USER \
    APACHE_RUN_GROUP=$CRUFT_GROUP \
    APACHE_DOCUMENT_ROOT=$CRUFT_CODE/public

RUN if [ -f /etc/alpine-release ]; then \
      addgroup -g $CRUFT_GID $CRUFT_GROUP && \
      adduser -h $CRUFT_HOME -G $CRUFT_GROUP -u $CRUFT_UID -D $CRUFT_USER && \
      addgroup $CRUFT_USER ssl-cert; \
    else \
      groupadd -g $CRUFT_GID $CRUFT_GROUP && \
      useradd -m -d $CRUFT_HOME -g $CRUFT_GROUP -G ssl-cert --uid $CRUFT_UID $CRUFT_USER; \
    fi; \
    mkdir -p $CRUFT_CODE && chown $CRUFT_USER:$CRUFT_GROUP $CRUFT_CODE && \
    echo "net.ipv4.ip_unprivileged_port_start=0" > /etc/sysctl.d/99-cruftman.conf && \
    sysctl -p && \
    sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

VOLUME $CRUFT_CODE

WORKDIR $CRUFT_CODE

# vim: set ft=dockerfile:
