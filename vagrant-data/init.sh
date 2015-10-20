#!/usr/bin/env bash

sudo apt-get update

#variables
dbPassword='password'
racingDatabase='rubixtrader_racing'
databaseSetupInfoBranch='Bx_aggregator'
schemaSql='schema.sql'
staticDataSql='static_data.sql'
testDataSql='test_data.sql'


sudo apt-get -y install git

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

}


function importData {
echo "importing file $1"
mysql -uroot -p$dbPassword  $racingDatabase < $1

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



echo "=========================setting up the database $racingDatabase======================================="
createDB $racingDatabase
cd /vagrant/racing_aggregator/db/mariadb
git checkout $databaseSetupInfoBranch
importData $schemaSql
importData $staticDataSql
importData $testDataSql
echo "========================================================================================================"


sudo cp /vagrant/vagrant-data/000-default.conf  /etc/apache2/sites-available/000-default.conf

sudo service apache2 restart