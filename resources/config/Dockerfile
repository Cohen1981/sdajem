ARG JOOMLA_VERSION
FROM joomla:${JOOMLA_VERSION}

LABEL version="1.0.0"
LABEL description="Joomla development environment with Xdebug"

ENV XDEBUG_PORT 9000

RUN yes | pecl install xdebug \
    && rm -rf /usr/local/etc/php/conf.d/opcache-recommended.ini \
    #&& echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \

EXPOSE 9000
