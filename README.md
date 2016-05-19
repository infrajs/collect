# Собирает javascript расширений infrajs в один файл
**Disclaimer:** Module is not complete and not ready for use yet.
Расширение для [infrajs/config](https://github.com/infrajs/config)

Обрабатывается свойство ```js``` в конфиге. Сборка скриптов доступна по адресу ```/vendor/infrajs/collect/js.php```

## Использование
```html
<head>
	<script async defer src="/vendor/infrajs/collect/js.php"></script>
</head>
<body>
	...
	<script>
		window.addEventListener('load', function () { //if jquery in .infra.json you can't use $(function() {...
			alert('use infrajs or any loaded scripts like jquery');
		});
	</script>
	...
</body>
```
## Загрузка javascript определённых расширений
```html
	<script async defer src="/vendor/infrajs/collect/js.php?name=collect"></script>
```
```html
	<script async defer src="/vendor/infrajs/collect/js.php?name=event,tester"></script>
```