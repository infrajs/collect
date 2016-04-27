# Собирает javascript расширений infrajs в один файл
**Disclaimer:** Module is not complete and not ready for use yet.
Расширение для [infrajs/config](https://github.com/infrajs/config)

Обрабатывается свойство ```js``` в конфиге. Сборку скриптов доступна по адресу ```-collect/js.php```

# Использование
```html
<head>
	<script async defer src="/-config/js.php"></script>
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
