# install BALAFON PHP FRAMEWORK

## In docker

- Create a image container from ```ubuntu``` image
- In created container run

```
apt-get update 
apt-get install curl -y && \
apt-get install unzip -y && \
apt-get install vim -y && \
apt-get install php -y && \
apt-get isntall php-zip -y && \
apt-get isntall php-mysqli -y && \
apt-get isntall php-curl -y 
```

## apache configuration 

```conf

DocumentRoot path_to_public_root_folder

<Directory path_to_public_root_folder>
    Options +FollowSymLinks -MultiViews -Indexes
    Order deny,allow
    AllowOverride none
    Allow from all
    Require all granted

    <IfModule rewrite_module>
        RewriteEngine on
        RewriteCond "%{REQUEST_FILENAME}" !-d
        RewriteCond "%{REQUEST_FILENAME}" !-f
        RewriteRule ^(.)+$ "/index.php?rwc=1" [QSA,L]
    </IfModule>
</Directory>
```


- activate mod rewrite

```
a2enmod rewrite
```

- download library in website folder location 
```
curl -A firefox https://igkdev.com/balafon/get-download -o balafon.zip
```

- extract library to site application folder 

```
unzip balafon.zip -o src/application
```

- add balafon CLI to your environment PATH

```
export PATH=$PATH:$(pwd)/src/application/Lib/igk/bin
```

- unstall new site 

```
balafon --install-site --root_dir:src/public --application:src/application
```


# NOTE
you can check balafon cli --help for more options.