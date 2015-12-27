FROM debian:latest
MAINTAINER Igor Dubiy <netzver@gmail.com>

ADD pusher.php /var/pusher.php

RUN echo "deb http://http.us.debian.org/debian unstable main non-free contrib" >> /etc/apt/sources.list
RUN echo "deb-src http://http.us.debian.org/debian unstable main non-free contrib" >> /etc/apt/sources.list

RUN DEBIAN_FRONTEND=noninteractive \
    apt-get update && \
    apt-get install -y \
    php5 \
    php5-sqlite \
    mc \
    megatools \
    wget \
    curl && \
    apt-get clean && \
    apt-get purge && \
    rm -rf /tmp/*
