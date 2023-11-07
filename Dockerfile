FROM debian:buster

RUN apt-get update && apt-get install -y apache2
RUN apt-get install -y ca-certificates apt-transport-https wget curl cron htop

RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    echo "deb https://packages.sury.org/php/ buster main" > /etc/apt/sources.list.d/php.list

RUN apt-get update
RUN apt-get install -y php8.1 php8.1-curl php8.1-mysql php8.1-bcmath php8.1-mbstring php8.1-xml php8.1-soap php8.1-gd php8.1-zip

RUN a2enmod php8.1
RUN a2enmod rewrite

ADD docker/apache/deep_auth.conf /etc/apache2/sites-available/deep_auth.conf
ADD docker/crontab/root /var/spool/cron/crontabs/root

RUN chmod 0644 /var/spool/cron/crontabs/root
RUN crontab /var/spool/cron/crontabs/root

RUN a2ensite deep_auth.conf
RUN a2dissite 000-default.conf
RUN a2disconf other-vhosts-access-log

COPY docker/start.sh /
RUN chmod a+x /start.sh
CMD ["/start.sh"]
