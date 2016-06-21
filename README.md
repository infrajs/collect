# Сборщик css и javascript в один файл

Расширение собирает и объединяет javascript-файлы в один по адресу ```/vendor/infrajs/collect/?js``` 
Расширение собирает и объединяет css-файлы в один по адресу ```/vendor/infrajs/collect/?css``` 

Если в проекте настроен .htaccess на работу с коротким путями [infrajs/router](https://github.com/infrajs/router) будут работать короткие адреса

```/-collect/?js``` 

```/-collect/?css```

их и рекомендуется использовать.

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
	"bower_components/jquery":{
		"js":"dist/jquery.min.js"
	},
	"bootstrap":{
		"js":"js/bootstrap.min.js"
	},
	"bower_components/flexslider":{
		"js":"jquery.flexslider-min.js"
	},
	"css":{
		"css":["animate.css","style.css"]
	}
}
```

Все пути в css-файлах должны быть указаны от корня проекта "/" или быть абсолютными. 
Стили **bootstrap** и **flexslider** содержат пути относительно своего расположения их придётся загружать отдельно или соответствующее инструкции работать не будут.

## Порядок выполнения - dependencies
Порядок в котором загружается код, определяется порядком упоминания файлов в **.infra.json**. 
Для явного указания зависимостей используется свойство **dependencies**, в котором можно перечислить расширения, которые должны быть загружены "до".

```json
{
	"bower_components/jquery":{
		"js":"dist/jquery.min.js"
	},
	"bootstrap":{
		"dependencies":"bower_components/jquery",
		"js":"js/bootstrap.min.js"
	},
	"bower_components/flexslider":{
		"dependencies":["bootstrap","bower_components/jquery"],
		"js":"jquery.flexslider-min.js"
	},
	"css":{
		"dependencies":"popup",
		"css":["animate.css","style.css"]
	}
}
```

Свойство **dependencies** используется, когда код css и js загружаются из нескольких **.infra.json** и порядок не очевиден.

В примере указано ещё одно расширение **popup**, которое после установки само по себе поддерживает автоматическое подключение через **infrajs/collect** и содержит свой **.infra.json** и соответствено добавляет к загрузке свои ситили. 

Чтобы **style.css** самого проекта был выполнен в последнюю очередь и мог переопределить стили **popup** необходимо указать в **dependencies** расширение **popup**.

**popup** установлен по адресу vendor/infrajs/popup/, но к нему можно обращаться по короткому адресу, так как в папке vendor/infrajs/popup/ есть файл .infra.json. Такую функциональность предоставляет устанавливаемое расширение [infrajs/config-search](https://github.com/infrajs/config-search)

## Загрузка js и css отдельных расширений
```html
	<script async defer src="/vendor/infrajs/collect/?js&name=event"></script>
```
```html
	<script async defer src="/vendor/infrajs/collect/?js&name=event,tester"></script>
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
