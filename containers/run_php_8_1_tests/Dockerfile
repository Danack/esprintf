FROM debian:10

USER root

# Get Debian up-to-date
RUN apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y git \
    mariadb-client wget curl \
    ca-certificates lsb-release apt-transport-https gnupg bsdmainutils

# Install 3rd party PHP 8.1 packages
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee -a /etc/apt/sources.list.d/php.list
RUN curl -L https://packages.sury.org/php/apt.gpg | apt-key add -

RUN  apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y php8.1 php8.1-common php8.1-cli php8.1-fpm \
    php8.1-mysql  php8.1-curl php8.1-xml php8.1-mbstring \
    php8.1-intl php8.1-xdebug php8.1-zip

RUN echo "xdebug.mode=coverage" >> /etc/php/8.1/cli/conf.d/20-xdebug.ini


# Make the default directory you
WORKDIR /var/app

ADD entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]