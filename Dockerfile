FROM debian:jessie

MAINTAINER Yuvi Panda <yuvipanda@riseup.net>

RUN apt-get update
RUN apt-get install --yes --no-install-recommends lighttpd php5-cgi  ca-certificates
RUN mkdir -p /data/project/nagf
RUN chown -R www-data:www-data /data

ADD . /data/project/nagf/

RUN chmod 777 /data/project/nagf/cache
EXPOSE 8080
ENTRYPOINT /usr/sbin/lighttpd -f /data/project/nagf/lighttpd.conf -D
