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

passing parameters to layout

```php
//#{{% expression %}}
```


##### default expression

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

- 20232109
    + add model - map
    + add CurlHttpClient session request support 
    + remove igk_db_table_select_relationnal_where, igk_sys_srv_nocache_request, igk_html_validate_error 
- 20220714
    + add igk_css_request_ctrl global function 
    + OPS update css dynamic file from controller


@ C.A.D BONDJE DOUE

