# Balafon
Balafon php web framework
## INSTALL
prerequisites

```
php7.3+ - Apache

module php requis
php-zip
php-curl
php-mysqli
php-gd
```

### For AAPACHE
Apache serveur avec module rewrite
```
a2enmod rewrite
a2enmod ssl
a2enmod header
```

### In DOCKER 

## Concepts

### Controllers

### Projects
### Views

Views are ".phtml" files located in Project's Views folder. 

#### Views options
passing layout compilation view is to 
```php
//%{{ expression }}
```
where expression can be 
| Name  | Description |
| ----- | ------------ |
| @MainLayout| |
| @Import('*other views - compile, file*')| |
| @Include('*include file not compile*') | |


### Modules

### PCSS Binding

### Article - Template - Binding


## Data Adapter

## FIX

- 20220714
    + add igk_css_request_ctrl global function 
    + OPS update css dynamic file from controller

