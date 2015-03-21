# styles-split

>Split a CSS file based on selectors. (e.g. removing old IE stylesheets etc.)

#Overview

Configuration is in the head of the script

```php 
$workingPath = 'd:/temp'; //Working path

$cssFiles = $workingPath . '/css'; // working folder with your css files

$cssLocales = $workingPath . '/css/'; // css file with removed rules

$cssFixedFiles = $workingPath . '/css/'; // new css without specified rules

$reg = "#\.ie8|ie9#msiu"; // regex of selectors you want to remove

$component = 'ie'; // (main.css --> main-ie.css)
```

#Getting started
PHP is required. [Installation and Configuration](http://php.net/manual/en/install.php)

Once it has been installed simply run this in your command line
``` 
php styles-split.php
```
#Expected result
>Source: main.css
``` css
.example .bar,
.ie8 .example .bar,
.ie9 .example .bar {background:blue;}
.ie8 {background:red;}
.ie9 {background:orange;}
@media (max-width: 980px) {
  .ie9 {background:yellow;}
  .example .bar{background:red;}
}
```
>Generated file: main-ie.css
``` css
.ie8 .example .bar {background:blue;}
.ie9 .example .bar {background:blue;}
.ie8 {background:red;}
.ie9 {background:orange;}
@media (max-width: 980px) {
  .ie9 {background:yellow;}
}
```

>Generated file: main-rest.css
``` css
.example .bar {background:blue;}
@media (max-width: 980px) {
  .example .bar {background:red;}
}
```


