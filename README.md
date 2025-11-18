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



# Documentation Balafon - Fichiers .pcss

## Table des matières

1. [Introduction](#introduction)
2. [Structure de base](#structure-de-base)
3. [Tableaux disponibles](#tableaux-disponibles)
4. [Syntaxes de définition](#syntaxes-de-définition)
5. [Fonctionnalités avancées](#fonctionnalités-avancées)
6. [Responsive Design](#responsive-design)
7. [Exemples pratiques](#exemples-pratiques)
8. [Bonnes pratiques](#bonnes-pratiques)

---

## Introduction

Le fichier d'extension `.pcss` (Processed CSS) est un fichier PHP qui permet de définir dynamiquement des styles CSS pour votre projet Balafon. Interprété par PHP, il offre des fonctionnalités avancées comme la gestion de thèmes, l'importation de styles système, et le responsive design.

### Avantages des fichiers .pcss

- **Dynamisme** : Génération de CSS via PHP
- **Thématisation** : Gestion centralisée des couleurs et variables
- **Compatibilité** : Gestion automatique des préfixes navigateurs
- **Réutilisabilité** : Importation de styles système
- **Responsive** : Définitions spécifiques par taille d'écran

---

## Structure de base

Un fichier `.pcss` est un fichier PHP qui manipule des tableaux prédéfinis :

```php
<?php
// Définir des couleurs de thème
$cl["primary"] = "#3498db";
$cl["secondary"] = "#2ecc71";

// Définir des styles globaux
$def["body"] = [
    "margin" => "0",
    "padding" => "0",
    "font-family" => "Arial, sans-serif"
];
```

---

## Tableaux disponibles

### 1. `$def` - Définitions globales

Contient les styles CSS appliqués à tous les écrans.

```php
$def[".ma-classe"] = [
    "color" => "#333",
    "font-size" => "16px"
];
```

### 2. `$sm_screen` - Petits écrans

Définitions pour les petits écrans (tablettes).

```php
$sm_screen[".ma-classe"] = [
    "font-size" => "14px"
];
```

### 3. `$xsm_screen` - Écrans mobiles

Définitions pour les appareils mobiles.

```php
$xsm_screen[".ma-classe"] = [
    "font-size" => "12px",
    "padding" => "8px"
];
```

### 4. `$lg_screen` - Écrans larges

Définitions spécifiques aux écrans larges (desktop).

```php
$lg_screen[".container"] = [
    "max-width" => "1200px"
];
```

### 5. `$xlg_screen` - Écrans extra larges

Pour les très grands écrans.

```php
$xlg_screen[".container"] = [
    "max-width" => "1400px"
];
```

### 6. `$xxlg_screen` - Écrans supérieurs

Pour les écrans de très haute résolution.

```php
$xxlg_screen[".hero"] = [
    "height" => "100vh"
];
```

### 7. `$PTR` - Impression

Styles appliqués lors de l'impression.

```php
$PTR[".no-print"] = [
    "display" => "none"
];
```

### 8. `$cl` - Couleurs du thème

Définit les couleurs réutilisables dans le projet.

```php
$cl["primary-color"] = "#3498db";
$cl["text-color"] = "#333333";
$cl["background"] = "#ffffff";
```

### 9. `$root` - Variables CSS3

Définit les propriétés CSS attachées à `:root`.

```php
$root["--primary-color"] = "#3498db";
$root["--spacing-unit"] = "8px";
$root["--border-radius"] = "4px";
```

---

## Syntaxes de définition

### 1. Syntaxe avec tableau associatif (Recommandée)

La méthode recommandée pour définir des styles complexes :

```php
$def[".button"] = [
    "background-color" => "#3498db",
    "color" => "#ffffff",
    "padding" => "10px 20px",
    "border" => "none",
    "border-radius" => "4px",
    "cursor" => "pointer"
];
```

### 2. Syntaxe avec chaîne de caractères

Balafon accepte également les styles sous forme de chaîne CSS standard :

```php
$def[".info"] = "background-color: red; color: white; padding: 10px;";
```

**Important :** Vous pouvez combiner classe et sous-classe avec un point :

```php
$def[".info.card"] = "background-color: red;";
```

Ceci génère le sélecteur CSS `.info.card` (éléments ayant les deux classes).

**Exemples de sélecteurs valides :**

```php
// Classe simple
$def[".button"] = "color: blue;";

// Classes multiples
$def[".btn.primary"] = "background: blue;";

// Avec pseudo-classe
$def[".button:hover"] = "background: darkblue;";

// Combinaison
$def[".card.active:hover"] = "box-shadow: 0 4px 8px rgba(0,0,0,0.2);";
```

**Quand utiliser chaque syntaxe :**

- **Chaîne de caractères** : Pour des styles simples et rapides
  ```php
  $def[".text-red"] = "color: red;";
  ```

- **Tableau associatif** : Pour des styles complexes ou utilisant des fonctionnalités Balafon
  ```php
  $def[".button"] = [
      "background" => "[syscl:primary, #3498db]",
      "transition" => "[trans:all 0.3s ease]"
  ];
  ```

### 3. Importation de classes système avec accolades

Syntaxe : `{[type]:classe1 classe2 classe3}`

Permet d'importer plusieurs classes système séparées par des espaces, sans le préfixe ".".

```php
$def["body"] = "{sys:dispn alignl}";
```

**Équivalent à :**
```css
body {
    display: none;
    text-align: left;
}
```

### 4. Importation de styles nommés avec parenthèses

Syntaxe : `([type]:nom_du_style)`

Permet d'importer un style défini ailleurs dans le thème.

```php
// Définir un style réutilisable
$def[".clear"] = [
    "clear" => "both",
    "display" => "block"
];

// Ou avec chaîne de caractères
$def[".clearfix"] = "clear: both; display: block;";

// L'utiliser dans un autre style
$def["body"] = "(:.clear)";
```

**Avec styles système :**
```php
$def["main"] = "(sys:.fitw)";
```

Ceci importe le style `.fitw` du thème système.

### 5. Importation de thème Google Fonts

Syntaxe : `(:.google-NomPolice)`

```php
$def[".home"] = "(:.google-Roboto)";
```

Charge et applique la police Google Roboto.

---

## Fonctionnalités avancées

### 1. Utilisation des couleurs système

Syntaxe : `[syscl:themeCouleur, couleur_par_defaut]`

```php
$def["body"] = [
    "background-color" => "[syscl:backgroundColor, #ffffff]",
    "color" => "[syscl:textColor, #333333]"
];
```

Si `backgroundColor` est défini dans `$cl`, il sera utilisé. Sinon, la valeur par défaut sera appliquée.

### 2. Transitions compatibles navigateurs

Syntaxe : `[trans:propriétés durée courbe]`

Balafon gère automatiquement les préfixes navigateurs (`-webkit-`, `-moz-`, etc.).

```php
$def[".button"] = [
    "transition" => "[trans:all 0.3s ease-in-out]"
];
```

**Génère automatiquement :**
```css
.button {
    -webkit-transition: all 0.3s ease-in-out;
    -moz-transition: all 0.3s ease-in-out;
    -o-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
}
```

### 3. Transformations compatibles navigateurs

Syntaxe : `[transform:propriété_transformation]`

```php
$def[".rotate"] = [
    "transform" => "[transform:rotate(45deg)]"
];

$def[".scale:hover"] = [
    "transform" => "[transform:scale(1.1)]"
];
```

**Génère :**
```css
.rotate {
    -webkit-transform: rotate(45deg);
    -moz-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
```

### 4. Variables CSS3

Utilisez `$root` pour définir des variables CSS réutilisables :

```php
$root["--primary-color"] = "#3498db";
$root["--spacing"] = "16px";
$root["--font-size-base"] = "16px";

$def[".button"] = [
    "background-color" => "var(--primary-color)",
    "padding" => "var(--spacing)",
    "font-size" => "var(--font-size-base)"
];
```

---

## Responsive Design

### Ordre de chargement

1. `$def` - Styles de base (tous écrans)
2. `$sm_screen` - Surcharge pour petits écrans
3. `$xsm_screen` - Surcharge pour mobiles
4. `$lg_screen` - Surcharge pour grands écrans
5. `$xlg_screen` - Surcharge pour très grands écrans
6. `$xxlg_screen` - Surcharge pour écrans supérieurs

### Exemple de responsive complet

```php
<?php
// Styles de base (mobile-first)
$def[".container"] = [
    "width" => "100%",
    "padding" => "16px",
    "margin" => "0 auto"
];

// Tablettes
$sm_screen[".container"] = [
    "max-width" => "768px",
    "padding" => "24px"
];

// Desktop
$lg_screen[".container"] = [
    "max-width" => "1024px",
    "padding" => "32px"
];

// Large desktop
$xlg_screen[".container"] = [
    "max-width" => "1280px"
];

// Extra large
$xxlg_screen[".container"] = [
    "max-width" => "1536px"
];
```

---

## Exemples pratiques

### Exemple 1 : Système de couleurs cohérent

```php
<?php
// Définir la palette de couleurs
$cl["primary"] = "#3498db";
$cl["primary-dark"] = "#2980b9";
$cl["secondary"] = "#2ecc71";
$cl["danger"] = "#e74c3c";
$cl["warning"] = "#f39c12";
$cl["text"] = "#333333";
$cl["text-light"] = "#7f8c8d";
$cl["background"] = "#ffffff";
$cl["border"] = "#ecf0f1";

// Utiliser les couleurs avec tableau associatif
$def[".btn-primary"] = [
    "background-color" => "[syscl:primary, #3498db]",
    "color" => "#ffffff",
    "border" => "none",
    "padding" => "10px 20px",
    "transition" => "[trans:background-color 0.2s ease]"
];

$def[".btn-primary:hover"] = [
    "background-color" => "[syscl:primary-dark, #2980b9]"
];

// Ou avec chaîne de caractères pour des styles simples
$def[".text-muted"] = "color: [syscl:text-light, #7f8c8d];";
$def[".bg-white"] = "background-color: [syscl:background, #ffffff];";
```

### Exemple 2 : Menu responsive

```php
<?php
// Variables
$root["--menu-height"] = "60px";
$root["--menu-transition"] = "0.3s";

// Menu de base
$def[".menu"] = [
    "height" => "var(--menu-height)",
    "background-color" => "[syscl:primary, #3498db]",
    "display" => "flex",
    "align-items" => "center",
    "padding" => "0 20px",
    "transition" => "[trans:all var(--menu-transition) ease]"
];

$def[".menu__items"] = [
    "display" => "flex",
    "gap" => "20px",
    "list-style" => "none"
];

// Mobile
$xsm_screen[".menu__items"] = [
    "position" => "fixed",
    "top" => "var(--menu-height)",
    "left" => "0",
    "right" => "0",
    "flex-direction" => "column",
    "background-color" => "[syscl:primary, #3498db]",
    "padding" => "20px",
    "transform" => "[transform:translateY(-100%)]",
    "transition" => "[trans:transform var(--menu-transition) ease]"
];

$xsm_screen[".menu__items.open"] = [
    "transform" => "[transform:translateY(0)]"
];
```

### Exemple 3 : Carte avec effet 3D

```php
<?php
$def[".card"] = [
    "background" => "[syscl:background, #ffffff]",
    "border-radius" => "8px",
    "padding" => "24px",
    "box-shadow" => "0 2px 8px rgba(0, 0, 0, 0.1)",
    "transition" => "[trans:all 0.3s ease]",
    "transform" => "[transform:translateY(0)]"
];

$def[".card:hover"] = [
    "box-shadow" => "0 8px 24px rgba(0, 0, 0, 0.15)",
    "transform" => "[transform:translateY(-4px)]"
];

$def[".card__title"] = [
    "font-size" => "24px",
    "font-weight" => "600",
    "color" => "[syscl:text, #333333]",
    "margin" => "0 0 16px 0"
];

$def[".card__content"] = [
    "color" => "[syscl:text-light, #7f8c8d]",
    "line-height" => "1.6"
];
```

### Exemple 4 : Grid responsive

```php
<?php
// Grid de base
$def[".grid"] = [
    "display" => "grid",
    "grid-template-columns" => "1fr",
    "gap" => "20px",
    "padding" => "20px"
];

// Tablette : 2 colonnes
$sm_screen[".grid"] = [
    "grid-template-columns" => "repeat(2, 1fr)",
    "gap" => "24px"
];

// Desktop : 3 colonnes
$lg_screen[".grid"] = [
    "grid-template-columns" => "repeat(3, 1fr)",
    "gap" => "32px"
];

// Large : 4 colonnes
$xlg_screen[".grid"] = [
    "grid-template-columns" => "repeat(4, 1fr)"
];
```

### Exemple 5 : Combinaison syntaxe chaîne et tableau

```php
<?php
// Styles simples avec chaîne
$def[".text-center"] = "text-align: center;";
$def[".text-bold"] = "font-weight: bold;";
$def[".mt-2"] = "margin-top: 16px;";

// Styles complexes avec tableau
$def[".card.featured"] = [
    "background" => "[syscl:primary, #3498db]",
    "color" => "white",
    "padding" => "24px",
    "border-radius" => "8px",
    "transform" => "[transform:scale(1)]",
    "transition" => "[trans:transform 0.3s ease]"
];

$def[".card.featured:hover"] = [
    "transform" => "[transform:scale(1.05)]"
];

// Classes multiples avec chaîne
$def[".btn.btn-small"] = "padding: 6px 12px; font-size: 12px;";
$def[".alert.warning"] = "background: #fff3cd; border: 1px solid #ffc107;";
```

---

## Bonnes pratiques

### 1. Organisation du fichier

```php
<?php
// ===========================
// 1. COULEURS DU THÈME
// ===========================
$cl["primary"] = "#3498db";
$cl["secondary"] = "#2ecc71";
// ...

// ===========================
// 2. VARIABLES CSS3
// ===========================
$root["--spacing-unit"] = "8px";
$root["--font-size-base"] = "16px";
// ...

// ===========================
// 3. STYLES RÉUTILISABLES
// ===========================
$def[".clearfix"] = [...];
$def[".visually-hidden"] = [...];

// ===========================
// 4. STYLES GLOBAUX
// ===========================
$def["*"] = [...];
$def["body"] = [...];
$def["h1"] = [...];

// ===========================
// 5. COMPOSANTS
// ===========================
$def[".button"] = [...];
$def[".card"] = [...];

// ===========================
// 6. RESPONSIVE
// ===========================
$sm_screen[".container"] = [...];
$xsm_screen[".menu"] = [...];
```

### 2. Nommage des couleurs

Privilégiez des noms sémantiques :

```php
// ✅ Bon
$cl["primary"] = "#3498db";
$cl["success"] = "#2ecc71";
$cl["text-primary"] = "#333333";

// ❌ Éviter
$cl["blue"] = "#3498db";
$cl["green"] = "#2ecc71";
$cl["dark-gray"] = "#333333";
```

### 3. Mobile-first

Définissez d'abord les styles mobiles dans `$def`, puis surchargez pour les écrans plus grands :

```php
// Mobile d'abord
$def[".text"] = [
    "font-size" => "14px"
];

// Desktop ensuite
$lg_screen[".text"] = [
    "font-size" => "16px"
];
```

### 4. Éviter les boucles de référence

```php
// ❌ Crée une boucle infinie
$def[".style-a"] = "(:.style-b)";
$def[".style-b"] = "(:.style-a)";

// ✅ Bon
$def[".base-style"] = ["color" => "red"];
$def[".style-a"] = "(:.base-style)";
$def[".style-b"] = "(:.base-style)";
```

### 5. Utiliser les variables CSS pour les valeurs répétitives

```php
// ✅ Bon
$root["--border-radius"] = "8px";
$root["--shadow"] = "0 2px 8px rgba(0, 0, 0, 0.1)";

$def[".card"] = [
    "border-radius" => "var(--border-radius)",
    "box-shadow" => "var(--shadow)"
];

$def[".button"] = [
    "border-radius" => "var(--border-radius)"
];
```

### 6. Commenter le code

```php
<?php
// Menu principal de navigation
// Responsive : devient un menu burger sur mobile
$def[".main-menu"] = [
    "display" => "flex",
    "align-items" => "center"
];

// Sur mobile, le menu se transforme en overlay
$xsm_screen[".main-menu"] = [
    "position" => "fixed",
    "top" => "0",
    "left" => "0"
];
```

### 7. Choisir entre chaîne et tableau

```php
// ✅ Chaîne : Pour des utilitaires simples
$def[".hidden"] = "display: none;";
$def[".text-center"] = "text-align: center;";
$def[".p-4"] = "padding: 32px;";

// ✅ Tableau : Pour des styles avec fonctionnalités Balafon
$def[".animated-button"] = [
    "background" => "[syscl:primary, #3498db]",
    "transition" => "[trans:all 0.3s ease]",
    "transform" => "[transform:scale(1)]"
];

// ✅ Tableau : Pour des styles multi-propriétés complexes
$def[".complex-card"] = [
    "display" => "flex",
    "flex-direction" => "column",
    "padding" => "20px",
    "border-radius" => "8px",
    "box-shadow" => "0 2px 8px rgba(0,0,0,0.1)",
    "background" => "[syscl:background, white]"
];

// ✅ Chaîne : Classes multiples simples
$def[".btn.small"] = "padding: 4px 8px; font-size: 12px;";
```

---

## Résumé des syntaxes

| Syntaxe | Usage | Exemple |
|---------|-------|---------|
| Tableau associatif | Styles complexes | `$def[".btn"] = ["color" => "red"]` |
| Chaîne CSS | Styles simples | `$def[".text-red"] = "color: red;"` |
| Classes multiples | Sélecteurs combinés | `$def[".btn.primary"] = "..."` |
| `[syscl:nom, defaut]` | Couleur système | `"color" => "[syscl:primary, #333]"` |
| `[trans:props]` | Transition | `"transition" => "[trans:all 0.3s ease]"` |
| `[transform:value]` | Transformation | `"transform" => "[transform:scale(1.1)]"` |
| `{sys:class1 class2}` | Import classes système | `"{sys:dispn alignl}"` |
| `(:.nom-style)` | Import style nommé | `"(:.clearfix)"` |
| `(sys:.nom)` | Import style système | `"(sys:.fitw)"` |
| `(:.google-Police)` | Import Google Font | `"(:.google-Roboto)"` |

---

## Conclusion

Les fichiers `.pcss` de Balafon offrent une solution puissante et flexible pour gérer les styles CSS de manière dynamique. En combinant PHP et CSS, vous bénéficiez de :

- **Thématisation avancée** avec gestion centralisée des couleurs
- **Compatibilité navigateurs** automatique
- **Responsive design** simplifié
- **Réutilisabilité** du code
- **Maintenabilité** améliorée

Utilisez cette documentation comme référence pour créer des thèmes cohérents et maintenables dans vos projets Balafon.


## author
@ C.A.D BONDJE DOUE