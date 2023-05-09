#!/bin/bash
  read -p "is Database fresh (empty) y/n ? " install_db
  if [ "$install_db" = "y" ]; then
    read -p "Admin username:" adminuser
    echo ""
    read -p "Admin password:" adminpassword
    echo ""
    read -p "Admin email:" adminemail
    echo ""
    read -p "Site fullname:" fullname
    echo ""
    read -p "Site short:" shortname
    echo ""
    #sudo -u nginx /usr/bin/php /var/www/site/html/admin/cli/install_database.php --lang=en --agree-license --fullname="$fullname" --shortname="$shortname" --adminuser="$adminuser" --adminpass="$adminpass" --adminemail="$adminemail"
    echo "-u nginx /usr/bin/php /var/www/site/html/admin/cli/install_database.php --lang=en --agree-license --fullname=\"$fullname\" --shortname=$shortname --adminuser=$adminuser --adminpass=\"$adminpassword\" --adminemail=$adminemail"
  else
    echo "-u nginx /usr/bin/php /var/www/site/html/admin/cli/upgrade.php --lang=en --non-interactive"
    #sudo -u nginx /usr/bin/php /var/www/site/html/admin/cli/upgrade.php --lang=en --non-interactive
  fi
