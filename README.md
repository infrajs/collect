# Сборщик css и javascript в один файл

- Расширение собирает и объединяет javascript-файлы в один по адресу ```/vendor/infrajs/collect/?js```
- Расширение собирает и объединяет css-файлы в один по адресу ```/vendor/infrajs/collect/?css``` 

Если в проекте настроены короткие пути [infrajs/router](https://github.com/infrajs/router) с .htaccess. Рекомендуется исопльзовать следующие пути:

- **/-collect/?js** 
- **/-collect/?css**

## Использование

В html Добавляем

```html
<head>
	<script src="/vendor/infrajs/collect/?js"></script>
	<link href="/vendor/infrajs/collect/?css" type="text/css" rel="stylesheet" />
</head>
```

## Описание 
Обрабатывается свойство **js**, **css** в конфигурационном файле [.infra.json](https://github.com/infrajs/config), который можно создать в корне проекта или в папках зависимостей.
После установки сборка javascript и css доступна по адресу ```/vendor/infrajs/collect/?js``` и ```/vendor/infrajs/collect/?css```.
Используется [кэш браузерный](https://github.com/infrajs/nostore) и [кэш серверый](https://github.com/infrajs/mem), [gzip, минификация](https://github.com/matthiasmullie/minify).

## Добавление своих css и js файлов
В проекте есть файлы css и js:

- bower_components/jquery/dist/jquery.min.js
- bootstrap/bootstrap.min.js
- bower_components/flexslider/jquery.flexslider-min.js,
- css/style.css
- css/animate.css

Добавить эти файлы можно с помощью корневого кофнига. В корне проекта создаётся файл **.infra.json** со следующим содержанием

```json
{
	"index":{
		"js":[
			"bower_components/jquery/dist/jquery.min.js",
			"bootstrap/js/bootstrap.min.js",
			"bower_components/flexslider/jquery.flexslider-min.js"
		],
		"css":[
			"css/animate.css",
			"css/style.css"
		]
	}
}
```

Все пути в css-файлах должны быть указаны от корня проекта "/" или быть абсолютными. 
Стили **bootstrap** и **flexslider** содержат пути относительно своего расположения их придётся загружать отдельно или соответствующее инструкции работать не будут.


## Загрузка js и css отдельных расширений
```html
	<script src="/vendor/infrajs/collect/?js&amp;name=event"></script>
```
```html
	<script src="/vendor/infrajs/collect/?js&amp;name=event,tester"></script>
```
## Путь от корня
Ключ в конфиге расширения
```json
{
	"-collect":"root"
}
``` 
будет означать что все пути js и css указаны от корня

## Требования
- composer
- php > 5.3
