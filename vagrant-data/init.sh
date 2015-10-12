#!/usr/bin/env bash

sudo apt-get update

#variables
racingSqlDumpFile='rubixtrader_racing.sql';
dbPassword='password'
racingDatabase='rubixtrader_racing'
racingMysqlDumpZIPFolder='rubixtrader_racing.sql'

sudo apt-get -y install unzip

sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt php5-mysql
sudo  php5enmod mcrypt
echo "install apache2 server in vagrant"
sudo apt-get -y install apache2
sudo a2enmod rewrite
sudo a2enmod proxy_http
sudo a2enmod headers



function createDB {
    cd /vagrant/vagrant-data
    echo "checking for $1 if exists"
    mysql -uroot -p$dbPassword -e "DROP DATABASE IF EXISTS $1"
    echo "creating the database $1"
    mysql -uroot -p$dbPassword -e "CREATE DATABASE $1;"
    echo "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;FLUSH PRIVILEGES;" | mysql -uroot -p$dbPassword
    echo "importing data sql file $2"
    mysql -uroot -p$dbPassword  $1 < $2
}






sudo apt-add-repository ppa:phalcon/stable
sudo apt-get -y update
sudo apt-get install -y php5-phalcon


#replace php.ini file with phalcon

sudo cp /vagrant/vagrant-data/phalcon_php.ini /etc/php5/apache2/php.ini

#install MySQL and setup the database /install phpmyadmin
###############################################
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $dbPassword"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $dbPassword"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $dbPassword"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $dbPassword"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $dbPassword"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect none"
sudo apt-get -y install mysql-server phpmyadmin

###############################################
# Install Unzip
###############################################
#sudo apt-get -y install unzip


cd /vagrant/vagrant-data
unzip "$racingMysqlDumpZIPFolder.zip"
createDB $racingDatabase  "$racingSqlDumpFile"
sudo rm -R -f $racingSqlDumpFile


sudo cp /vagrant/vagrant-data/000-default.conf  /etc/apache2/sites-available/000-default.conf

sudo service apache2 restart