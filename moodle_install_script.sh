#!/bin/bash

# clear the screen
tput clear

# Move cursor to screen location X,Y (top left is 0,0)
tput cup 3 15

# Set a foreground colour using ANSI escape
tput bold
tput setaf 1
echo "P A F C O   S C R I P T ..."
tput sgr0

tput cup 5 17
# Set reverse video mode
tput rev
echo -e "\e[36m P A F C O - A U T O - I N S T A L L A T I O N \e[m"
tput sgr0

tput cup 7 15
echo -e "\e[1;34m 1. Auto Install New Moodle 3.5\e[m"

tput cup 8 15
echo -e "\e[36m 2. Auto Install New Moodle 4.1\e[m"

tput cup 9 15
echo -e "\e[35m 3. Update Moodle 3.5 to 3.10.5\e[m"

tput cup 10 15
echo -e "\e[1;35m 4. Update Moodle 3.10 to 4.1\e[m"

tput cup 11 15
echo -e "\e[32m 5. Install Server \e[37mCentos 7\e[32m Requirment (Moodle 3.5-3.10) ...\e[m"

tput cup 12 15
echo -e "\e[1;32m 6. Install Server \e[37mCentos 7\e[1;32m Requirment (Moodle 4.1) ...\e[m"

tput cup 13 15
echo -e "\e[1;31m 7. Install Server \e[37mCentos 8\e[1;31m Requirment (Moodle 4.1) ...\e[m"

tput cup 14 15
echo -e "\e[36m 0. Exit\e[m"

# moodle install function
moodle() {
  clear
  echo ""
  echo -e "\e[33mPlease Insert Configuration config.php  ...\e[m"
  echo ""
  echo ""
  sleep 2
  read -p "Please Enter DATABASE IP Address : " DBIP
  if ping -q -c 1 -W 1 $DBIP >/dev/null; then
    echo ""
    echo -e "\e[36mConnection Succesfully With IP >>> $DBIP ...\e[m"
  else
    echo ""
    echo ""
    echo -e "\e[31m IPv4 is down check connection Beetwen Servers or Opened Icmp Request...\e[m"
    echo ""
    echo ""
    exit
  fi
  echo ""
  echo ""
  read -p "Please Enter DB NAME : " DBNAME
  echo ""
  read -p "Please Enter DB USER NAME : " DBUSER
  echo ""
  read -p "Please Enter DB PASSWORD : " DBPASS
  echo ""
  read -p "Please Enter IP Address or URL : " IP
  csf -a $DBIP
  mkdir -p /var/www/site/
  cd /var/www/site/
  wget http://demo.pafcodemo.com/lms-alone.zip
  unzip lms-alone.zip
  cd ..
  wget http://demo.pafcodemo.com/data100221.zip
  unzip data100221.zip
  chown -R nginx. /var/www/
  chmod -R 777 data100221
  mv /var/www/data100221/ /var/www/data$DBNAME
  echo -e "\e[36m SET CONFIG CONFIG.PHP ... \e[m"
  sleep 2
  echo "<?php  // Moodle configuration file

unset(\$CFG);
global \$CFG;
\$CFG = new stdClass();
\$CFG->dbtype    = 'sqlsrv';
\$CFG->dblibrary = 'native';
\$CFG->dbhost    = '$DBIP';
\$CFG->dbname    = '$DBNAME';
\$CFG->dbuser    = '$DBUSER';
\$CFG->dbpass    = '$DBPASS';
\$CFG->prefix    = 'mdl2_';
\$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '7233',
  'dbsocket' => '',
  'dbcollation'=> 'Latin1_General_CS_AS',
);
 //@error_reporting(E_ALL | E_STRICT);   // NOT FOR PRODUCTION SERVERS!
// @ini_set('display_errors', '1');         // NOT FOR PRODUCTION SERVERS!
/* @error_reporting(E_ALL | E_STRICT);
@ini_set('display_errors', '1');
\$CFG->debug = (E_ALL | E_STRICT);
\$CFG->debugdisplay = 1; */
\$CFG->wwwroot   = 'http://$IP';
\$CFG->dataroot  = '/var/www/data$DBNAME';
\$CFG->admin     = 'admin';

\$CFG->directorypermissions = 0777;

require_once(dirname(__FILE__) . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems! " >/var/www/site/html/config.php
  echo -e "\e[36m NOW START SET NGINX CONFIG ... \e[m"
  sleep 2
  echo "
upstream php-handler {

    server 127.0.0.1:9000;

}
#server {
#    listen   80;
#    listen   [::]:80;
#    server_name lms.eaz.pnu.ac.ir;
#    return 301 https://lms.eaz.pnu.ac.ir\$request_uri;

#}

    server {
       server_name  $IP;
       listen     80 default_server;
       #ssl_certificate "/etc/pki/nginx/trust.crt";
       #ssl_certificate_key "/etc/pki/nginx/key.key";
       #ssl_protocols       TLSv1.1 TLSv1.2 TLSv1.3;
       #ssl_ciphers         HIGH:!aNULL:!MD5;
       #ssl_prefer_server_ciphers on;
       #ssl_session_cache shared:SSL:1m;
        #ssl_session_timeout  10m;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
       #ssl_stapling on;
       # ssl_stapling_verify on;

        root         /var/www/site/html;
        index           index.php index.html
        # Load configuration files for the default server block.
        include /etc/nginx/default.d/*.conf;
        location / {
                try_files \$uri \$uri/ /index.php?\$args;
        }

        error_page 404 /404.html;
        location = /404.html {
        }

        error_page 500 502 503 504 /50x.html;
        location = /50x.html {
        }

        location ~ [^/]\.php(/|$) {
        fastcgi_split_path_info  ^(.+\.php)(/.+)$;
        fastcgi_index            index.php;
        fastcgi_pass             127.0.0.1:9000;
        include                  fastcgi_params;
        fastcgi_param   PATH_INFO       \$fastcgi_path_info;
        fastcgi_param   SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        }
        #location /data100429/ {
        #    internal;
        #    alias /var/www/data100429/; # ensure the path ends with /
        #}

    }" >/etc/nginx/sites-available/site.conf
  rm -rf /etc/nginx/sites-enabled/*
  ln -s /etc/nginx/sites-available/site.conf /etc/nginx/sites-enabled/site.conf
  rm -rf /var/www/data258/cache/*
  service nginx restart
  service php-fpm restart
  sudo -u nginx /usr/bin/php /var/www/site/html/admin/cli/upgrade.php --lang=en --non-interactive
}
moodle4() {
  clear
  echo ""
  echo -e "\e[33mPlease Insert Configuration config.php  ...\e[m"
  echo ""
  echo ""
  sleep 2
  read -p "Please Enter DATABASE IP Address : " DBIP
  if ping -q -c 1 -W 1 $DBIP >/dev/null; then
    echo ""
    echo -e "\e[36mConnection Succesfully With IP >>> $DBIP ...\e[m"
  else
    echo ""
    echo ""
    echo -e "\e[31m IPv4 is down check connection Beetwen Servers or Opened Icmp Request...\e[m"
    echo ""
    echo ""
    exit
  fi
  echo ""
  echo ""
  read -p "Please Enter DB NAME : " DBNAME
  echo ""
  read -p "Please Enter DB USER NAME : " DBUSER
  echo ""
  read -p "Please Enter DB PASSWORD : " DBPASS
  echo ""
  read -p "Please Enter IP Address or URL : " IP
  csf -a $DBIP
  mkdir -p /var/www/site/
  cd /var/www/site/
  wget https://github.com/SaeidSharafi/moodle/archive/refs/tags/pafco-v4.1-latest.zip
  unzip pafco-v4.1-latest.zip
  mv moodle-pafco-v4.1-latest html
  mkdir /var/www/data$DBNAME
  chmod -R 777 /var/www/data$DBNAME
  echo -e "\e[36m SET CONFIG CONFIG.PHP ... \e[m"
  sleep 2
  echo "<?php  // Moodle configuration file

unset(\$CFG);
global \$CFG;
\$CFG = new stdClass();
\$CFG->dbtype    = 'sqlsrv';
\$CFG->dblibrary = 'native';
\$CFG->dbhost    = '$DBIP';
\$CFG->dbname    = '$DBNAME';
\$CFG->dbuser    = '$DBUSER';
\$CFG->dbpass    = '$DBPASS';
\$CFG->prefix    = 'mdl2_';
\$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '7233',
  'dbsocket' => '',
  'dbcollation'=> 'Latin1_General_CS_AS',
);
 //@error_reporting(E_ALL | E_STRICT);   // NOT FOR PRODUCTION SERVERS!
// @ini_set('display_errors', '1');         // NOT FOR PRODUCTION SERVERS!
/* @error_reporting(E_ALL | E_STRICT);
@ini_set('display_errors', '1');
\$CFG->debug = (E_ALL | E_STRICT);
\$CFG->debugdisplay = 1; */
\$CFG->wwwroot   = 'http://$IP';
\$CFG->dataroot  = '/var/www/data$DBNAME';
\$CFG->admin     = 'admin';

\$CFG->directorypermissions = 0777;

require_once(dirname(__FILE__) . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems! " >/var/www/site/html/config.php
  echo -e "\e[36m NOW START SET NGINX CONFIG ... \e[m"
  sleep 2
  echo "
upstream php-handler {

    server 127.0.0.1:9000;

}
#server {
#    listen   80;
#    listen   [::]:80;
#    server_name lms.eaz.pnu.ac.ir;
#    return 301 https://lms.eaz.pnu.ac.ir\$request_uri;

#}

    server {
       server_name  $IP;
       listen     80 default_server;
       #ssl_certificate "/etc/pki/nginx/trust.crt";
       #ssl_certificate_key "/etc/pki/nginx/key.key";
       #ssl_protocols       TLSv1.1 TLSv1.2 TLSv1.3;
       #ssl_ciphers         HIGH:!aNULL:!MD5;
       #ssl_prefer_server_ciphers on;
       #ssl_session_cache shared:SSL:1m;
        #ssl_session_timeout  10m;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
       #ssl_stapling on;
       # ssl_stapling_verify on;

        root         /var/www/site/html;
        index           index.php index.html
        # Load configuration files for the default server block.
        include /etc/nginx/default.d/*.conf;
        location / {
                try_files \$uri \$uri/ /index.php?\$args;
        }

        error_page 404 /404.html;
        location = /404.html {
        }

        error_page 500 502 503 504 /50x.html;
        location = /50x.html {
        }

        location ~ [^/]\.php(/|$) {
        fastcgi_split_path_info  ^(.+\.php)(/.+)$;
        fastcgi_index            index.php;
        fastcgi_pass             127.0.0.1:9000;
        include                  fastcgi_params;
        fastcgi_param   PATH_INFO       \$fastcgi_path_info;
        fastcgi_param   SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        }
        #location /data100429/ {
        #    internal;
        #    alias /var/www/data100429/; # ensure the path ends with /
        #}

    }" >/etc/nginx/sites-available/site.conf
  rm -rf /etc/nginx/sites-enabled/*
  ln -s /etc/nginx/sites-available/site.conf /etc/nginx/sites-enabled/site.conf
  service nginx restart
  service php-fpm restart
  sudo -u nginx /usr/bin/php /var/www/site/html/admin/cli/upgrade.php --lang=en --non-interactive
}
# moodle update funciton
update() {
  #yum --enablerepo=epel -y install sshpass
  mv /var/www/site/html /var/www/site/older_html
  cd /var/www/site/
  wget http://212.33.197.201/moodle-3.10.5-dont-toch.zip
  unzip moodle-3.10.5-dont-toch.zip
  mv moodle-3.10.5 html
  cp older_html/config.php html/
  echo "\$CFG->disablelogintoken = true;" >>/var/www/site/html/config.php
  cd /var/www/site/html/
  sudo -u nginx /usr/bin/php admin/cli/upgrade.php --lang=en --non-interactive

}
updateM4() {
  #yum --enablerepo=epel -y install sshpass
  mv /var/www/site/html /var/www/site/older_html
  cd /var/www/site/ || exit
  wget https://github.com/SaeidSharafi/moodle/archive/refs/tags/pafco-v4.1-latest.zip
  unzip pafco-v4.1-latest.zip
  mv moodle-pafco-v4.1-latest html
  cp older_html/config.php html/
  cd /var/www/site/html/ || exit
  sudo -u nginx /usr/bin/php admin/cli/upgrade.php --lang=en --non-interactive

}
# install Requirement function
Requirement() {
  yum update -y
  yum install ntp wget gcc gcc-c++ flex bison make bind bind-libs bind-utils openssl yum-utils openssl-devel perl quota libaio libcom_err-devel libcurl-devel gd zlib-devel zip unzip libcap-devel cronie bzip2 cyrus-sasl-devel perl-ExtUtils-Embed autoconf automake libtool which patch mailx bzip2-devel lsof glibc-headers kernel-devel expat-devel db4-devel -y
  yum install -y chrony
  systemctl start chronyd && systemctl enable chronyd
  timedatectl set-ntp true
  timedatectl set-timezone Asia/Tehran
  yum install epel-release yum-utils vim net-tools -y
  echo "alias vi='vim'" >>.bashrc && source .bashrc
  yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm -y
  yum-config-manager --enable remi-php73 -y
  yum --enablerepo=remi install php-intl -y
  yum install php-pecl-zip -y
  yum install php php-common php-opcache php-mcrypt php-cli php-gd php-curl php-mysql php-devel php-soap php-xml php-mbstring php-xmlrpc -y
  curl https://packages.microsoft.com/config/rhel/7/prod.repo >/etc/yum.repos.d/mssql-release.repo
  ACCEPT_EULA=Y yum install -y msodbcsql mssql-tools unixODBC-devel
  cd /usr/local/src || exit
  wget https://pecl.php.net/get/sqlsrv-5.9.0.tgz
  tar -zxvf sqlsrv-5.9.0.tgz
  cd sqlsrv-5.9.0 || exit
  phpize
  ./configure
  make
  make install
  echo "extension=sqlsrv.so" >>/etc/php.d/20-pdo.ini
  cd /usr/local/src || exit
  wget https://pecl.php.net/get/pdo_sqlsrv-5.9.0.tgz
  tar -zxvf pdo_sqlsrv-5.9.0.tgz
  cd pdo_sqlsrv-5.9.0 || exit
  phpize
  ./configure
  make
  make install
  echo "extension=pdo_sqlsrv.so" >>/etc/php.d/20-pdo.ini
  php -v
  echo ""
  echo ""
  read -p "Do You Want Install Nginx And Php-Fpm y/n ? " nginx
  if [ "$nginx" != "n" ]; then
    yum install nginx php-fpm -y
  else
    exit
  fi
}

# install Requirement function
RequirementM41() {
  yum update -y
  yum install ntp wget gcc gcc-c++ flex bison make bind bind-libs bind-utils openssl yum-utils openssl-devel perl quota libaio libcom_err-devel libcurl-devel gd zlib-devel zip unzip libcap-devel cronie bzip2 cyrus-sasl-devel perl-ExtUtils-Embed autoconf automake libtool which patch mailx bzip2-devel lsof glibc-headers kernel-devel expat-devel db4-devel -y
  yum install -y chrony
  systemctl start chronyd && systemctl enable chronyd
  timedatectl set-ntp true
  timedatectl set-timezone Asia/Tehran
  yum install epel-release yum-utils vim net-tools -y
  echo "alias vi='vim'" >>.bashrc && source .bashrc
  major=$(cat /etc/centos-release | tr -dc '0-9.' | cut -d \. -f1)
  if [ "$major" == 8 ]; then
    yum install -y https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm
    yum install http://rpms.remirepo.net/enterprise/remi-release-8.rpm -y
  else
    yum install -y https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
    yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm -y
  fi
  yum-config-manager --enable remi-php81 -y
  yum --enablerepo=remi install php-intl -y
  yum install php-pecl-zip -y
  yum install php php-common php-opcache php-mcrypt php-cli php-gd php-curl php-mysql php-devel php-soap php-xml php-mbstring php-xmlrpc -y
  curl https://packages.microsoft.com/config/rhel/7/prod.repo >/etc/yum.repos.d/mssql-release.repo
  yum remove unixODBC-utf16 unixODBC-utf16-deve
  ACCEPT_EULA=Y yum install -y msodbcsql18
  ACCEPT_EULA=Y yum install -y mssql-tools18
  echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >>~/.bashrc
  source ~/.bashrc
  yum install -y unixODBC-devel
  yum install php-sqlsrv
  php -v
  echo ""
  echo ""
  read -p "Do You Want Install Nginx y/n ? " nginx
  if [ "$nginx" != "n" ]; then
    yum install nginx -y
  fi
  read -p "Do You Want Install Php-Fpm y/n ? " phpfpm
  if [ "$phpfpm" != "n" ]; then
    yum install php-fpm -y
  else
    exit
  fi
}
# install Requirement function
RequirementCentos8M41() {
  yum update -y
  yum install ntp wget gcc gcc-c++ flex bison make bind bind-libs bind-utils openssl yum-utils openssl-devel perl quota libaio libcom_err-devel libcurl-devel gd zlib-devel zip unzip libcap-devel cronie bzip2 cyrus-sasl-devel perl-ExtUtils-Embed autoconf automake libtool which patch mailx bzip2-devel lsof glibc-headers kernel-devel expat-devel db4-devel -y
  yum install -y chrony
  systemctl start chronyd && systemctl enable chronyd
  timedatectl set-ntp true
  timedatectl set-timezone Asia/Tehran
  yum install epel-release yum-utils vim net-tools -y
  echo "alias vi='vim'" >>.bashrc && source .bashrc
  major=$(cat /etc/centos-release | tr -dc '0-9.' | cut -d \. -f1)
  yum install -y https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm
  yum install http://rpms.remirepo.net/enterprise/remi-release-8.rpm -y

  yum-config-manager --enable remi-php81 -y
  yum --enablerepo=remi install php-intl -y
  yum install php-pecl-zip -y
  yum install php php-common php-opcache php-mcrypt php-cli php-gd php-curl php-mysql php-devel php-soap php-xml php-mbstring php-xmlrpc -y
  curl https://packages.microsoft.com/config/rhel/7/prod.repo >/etc/yum.repos.d/mssql-release.repo
  yum remove unixODBC-utf16 unixODBC-utf16-deve
  ACCEPT_EULA=Y yum install -y msodbcsql18
  ACCEPT_EULA=Y yum install -y mssql-tools18
  echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >>~/.bashrc
  source ~/.bashrc
  yum install -y unixODBC-devel
  yum install php-sqlsrv
  php -v
  echo ""
  echo ""
  read -p "Do You Want Install Nginx y/n ? " nginx
  if [ "$nginx" != "n" ]; then
    yum install nginx -y
  fi
  read -p "Do You Want Install Php-Fpm y/n ? " phpfpm
  if [ "$phpfpm" != "n" ]; then
    yum install php-fpm -y
  else
    exit
  fi
}
# Set bold mode
tput bold

while true; do
  tput cup 16 15
  read -p "Enter your choice [0-7] : " choice
  case $choice in
  1)
    moodle
    break
    ;;
  2)
    moodle4
    break
    ;;
  3)
    update
    break
    ;;
  4)
    updateM4
    break
    ;;
  5)
    Requirement
    break
    ;;
  6)
    RequirementM41
    break
    ;;
  7)
    RequirementCentos8M41
    break
    ;;
  0)
    clear
    exit 1
    ;;
  *)
    tput cup 17 15
    tput rc
    echo -e 'Invalid input' >&2
  esac
done

tput clear
tput sgr0
tput rc
