# Balafon

- Balafon php web framework


## INSTALL

- requirement

-- `php7.3+` + `Apache`

### php's required module
- php-zip
- php-curl
- php-mysqli
- php-gd


### APACHE's required module 

- a2enmod rewrite
- a2enmod ssl
- a2enmod header


## Install DOCKER container

## Concepts

### Controllers

### Projects

### Views

Views are `.phtml` or `.bview` files located in Project's Views folder. 

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


### Article - Template - Binding


### Themes
#### PCSS Binding
#### .bcss file specification

### Data Adapter

## author
@ C.A.D BONDJE DOUE