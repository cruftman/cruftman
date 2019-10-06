#!/bin/sh

# If you would like to do some extra provisioning you may
# add any commands you wish to this file and they will
# be run after the Homestead machine is provisioned.
#
# If you have user-specific configurations you would like
# to apply, you may also create user-customizations.sh,
# which will be run after this script.

# If you're not quite ready for Node 12.x
# Uncomment these lines to roll back to
# v11.x or v10.x

# Remove Node.js v12.x:
#sudo apt-get -y purge nodejs
#sudo rm -rf /usr/lib/node_modules/npm/lib
#sudo rm -rf //etc/apt/sources.list.d/nodesource.list

# Install Node.js v11.x
#curl -sL https://deb.nodesource.com/setup_11.x | sudo -E bash -
#sudo apt-get install -y nodejs

# Install Node.js v10.x
#curl -sL https://deb.nodesource.com/setup_10.x | sudo -E bash -
#sudo apt-get install -y nodejs

# Install OpenLDAP server
cat > /tmp/debconf-slapd.conf << 'EOF'
slapd slapd/password1 password admin
slapd slapd/internal/adminpw password admin
slapd slapd/internal/generated_adminpw password admin
slapd slapd/password2 password admin
slapd slapd/unsafe_selfwrite_acl note
slapd slapd/purge_database boolean false
slapd slapd/domain string example.org
slapd slapd/ppolicy_schema_needs_update select abort installation
slapd slapd/invalid_config boolean true
slapd slapd/move_old_database boolean false
slapd slapd/backend select HDB
slapd shared/organization string Example Org.
slapd slapd/dump_database_destdir string /var/backups/slapd-VERSION
slapd slapd/no_configuration boolean false
slapd slapd/dump_database select when needed
slapd slapd/password_mismatch note
EOF

DEBIAN_FRONTEND=noninteractive sudo debconf-set-selections /tmp/debconf-slapd.conf
DEBIAN_FRONTEND=noninteractive sudo apt install -y slapd ldap-utils


for s in dhcp iredmail radius; do
  if ! ( sudo ldapsearch -Q -LLL -Y EXTERNAL -H ldapi:/// -b 'cn=schema,cn=config' "cn={*}${s}" dn | grep -q '^\s*dn:' )
  then
    sudo ldapadd -Q -Y EXTERNAL -H ldapi:/// -f "/vagrant/ldap/schema/$s.ldif"
  fi
done

if [ -z "$ACL_LDIF" ]; then
  ACL_LDIF="/home/vagrant/code/tests/resources/ldap/acl.ldif";
fi

if [ -f "$ACL_LDIF" ]; then
  sudo ldapmodify -Y EXTERNAL -H ldapi:/// -f "$ACL_LDIF";
fi

if [ -z "$BOOTSTRAP_LDIF" ]; then
  BOOTSTRAP_LDIF="/home/vagrant/code/tests/resources/ldap/bootstrap.ldif";
fi

if [ -f "$BOOTSTRAP_LDIF" ]; then
  # Delete old LDAP entries first...
  ldapsearch -LLL -H ldapi:/// -D cn=admin,dc=example,dc=org -w admin \
    -s one -b 'dc=example,dc=org' '(&(objectclass=*)(!(cn=admin)))' 'dn' | \
    awk -F': ' '$1~/^\s*dn/ {print $2}' | \
    ldapdelete -H ldapi:/// -D cn=admin,dc=example,dc=org -w admin -r;
  # ... then load new ones...
  ldapadd -H ldapi:/// -D cn=admin,dc=example,dc=org -w admin -f "$BOOTSTRAP_LDIF";
fi

sudo service avahi-daemon restart
