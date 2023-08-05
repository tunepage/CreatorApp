# CreatorApp

CreatorApp — небольшой PHPSDK для взаимодействия с Битрикс24 через <a href="https://www.bitrix24.ru/apps/app/tunepage.app_creator/" target="_blank">«Конструктор приложений»</a>.


## Описание
Все запросы выполняются быстро, параллельно и гарантировано, с обработкой ошибок.

Режим выполнения запросов:
<ul>
<li>Онлайн (вызов call)</li>
<li>Поставить запрос на выполнение в быструю очередь, с поддержкой callback (вызов turn)</li>
</ul>

### Общие рекомендации

Чтобы не превысить лимиты со стороны портала Битрикс24 (2 запроса в секунду), а также проще регулировать свои северные ресурсы и сетевые соединения, используйте постановку запросов в очередь, т.е. <i>turn</i>:
```php
CApp::turn( $method, $params, $callback );
```

Синтаксис формирования запросов к Битрикс24 совпадает с примерами из [официальной документации](https://dev.1c-bitrix.ru/rest_help/), а для методов с <i>turn</i> при необходимости можно указать callback, которому будет отправлен id выполненного задания, чтобы могли получить результат. Подробнее в примерах.


## Установка

1. Cкачайте файл capp.php и подключите в проект
```php
require_once('src/capp.php');
```

2. Укажите ключ (вместо ***), который получили через <a href="https://www.bitrix24.ru/apps/app/tunepage.app_creator/" target="_blank">«Конструктор приложений»</a>
```php
$b24CreatorAppKey = '***';
```

3. Укажите ключ member_id портала Битрикс24, к которому нужно отправить запрос. Для примера, если приложение открывается в интерфейсе Битрикс24, то можно указать из данных, которые были переданы приложению:
```php
$memberId = $_REQUEST['member_id'];
```

4. Инициализируйте библиотеку
```php
CApp::newCreatorAppClient($b24CreatorAppKey, $memberId);
```

### Для выполнения запроса от конкретного пользователя 
5. Инициализируйте пользователя одним из двух вариантов:
```php
// Доступно для вызова call. Подходит, если известны авторизационные данные пользователя,
// например, при открытии приложения/встройки или
// получения активности от портала под пользователем, например, событие, робот и т.п.
// При открытии приложения можно использовать следующие 3 переменные:
$timestamp = time();
$authId = $_REQUEST['AUTH_ID'];
$refreshId = $_REQUEST['REFRESH_ID'];
CAppCurrent::newClient($timestamp, $authId, $refreshId);

// Доступно для вызова turn. Подходит, если известен ID пользователя на портале Битрикс24.
CAppCurrent::newClientByUserID($userId);
```

## Доступные вызовы

```php
// Основные
CApp::turn(...)
CApp::call(...)

// Получение результата для всех вызовов turn по turn_id, который вернется на указанный в запросе callback
CApp::result(...)

// Пакетный запрос (до 50 в 1 запросе)
CApp::turnBatch(...)
CApp::callBatch(...)

// От пользователя
CAppCurrent::turn(...)
CAppCurrent::call(...)
```

> Синтаксис запросов для вызовов _CAppCurrent_ совпадает с _CApp_
> Для пакетных запросов - примеры из [документации Битрикс24](https://dev.1c-bitrix.ru/rest_help/)


## Примеры 

### Вызов в режиме онлайн (не рекомендуется)

```php
// Отправить запрос и получить ответ
echo '<PRE>';
print_r(CApp::call(
   'crm.lead.add',
   [
      'fields' =>[
          'TITLE' => 'Название лида',//Заголовок*[string]
          'NAME' => 'Имя',//Имя[string]
          'LAST_NAME' => 'Фамилия',//Фамилия[string]
      ]
   ])
);
echo '</PRE>';
```

### Отправить запрос на исполнение, без ожидания результата
```php
// Поставить запрос в очередь без callback
echo '<PRE>';
print_r(CApp::turn('profile'));
echo '</PRE>';

echo '<PRE>';
print_r(CApp::turn(
	'crm.deal.list', 
	[
		'select' => ['TITLE']
	]
));
echo '</PRE>';
```


### Отправить запрос на исполнение с обратным вызовом, когда будет готов результат 
```php
// Поставить запрос в очередь c callback
echo '<PRE>';
print_r(CApp::turn(
    'profile', 
    [], //пустой массив, если не нужно указывать параметры
    'https://example.com/index.php'  //адрес для callback
));
echo '</PRE>';

echo '<PRE>';
print_r(CApp::turn(
    'crm.deal.list', 
    [
        'select' => ['TITLE',]
    ],
    'https://example.com/index.php'  //адрес для callback
));
echo '</PRE>';
```

### Запрос результата
```php
// К адресу обратного вызова будет добавлен turn_id, по которому можно получить готовый результат
echo '<PRE>';
print_r(CApp::result($_REQUEST['turn_id']);
echo '</PRE>';
```


### Пакетный запрос с callback
```php
$arData = [];

// Добавляем запросы в массив
$arData['add_lead'] = [
	'method' => 'crm.lead.add',
	'params' => [
		'fields' =>  [
			'TITLE' => 'Название лида',//Заголовок*[string]
			'NAME' => 'Имя',//Имя[string]
			'LAST_NAME' => 'Фамилия',//Фамилия[string]
		]
	]
];
$arData['get_lead'] = [
	'method' => 'crm.lead.get',
	'params' => [
		'id' => '$result[add_lead]'
	]
];

// отправляем запрос
echo '<PRE>';
print_r(CAppCurrent::tunr(
	$arData,
	0,	//Флаг "прерывать исполнение пакета в при возникновении ошибки". По умолчанию - 0
	'https://example.com/index.php'  //адрес для callback
);
echo '</PRE>';
```
