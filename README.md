# CreatorApp

CreatorApp — небольшой PHPSDK для взаимодействия с Битрикс24 через <a href="https://www.bitrix24.ru/apps/app/tunepage.app_creator/" target="_blank">«Конструктор приложений»</a>).


## Описание
Доступно:
<ul>
<li>Режим Онлайн или Постановка на выполнение (например, с callback)</li>
<li>Все запросы выполняются быстро, параллельно и гарантировано, с обработкой ошибок</li>
</ul>


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


## Примеры 

### Простой вызов

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



## Общие рекомендации

Чтобы не превысить лимиты со стороны портала Битрикс24 (2 запроса в секунду), а также проще регулировать свои северные ресурсы и сетевые соединения, используйте постановку запросов в очередь, т.е. <i>turn</i>:
```php
CApp::turn( $method, $params, $callback );
```

Как можно заметить, синтаксис формирования запросов к Битрикс24 совпадает примерами из [официальной документации](https://dev.1c-bitrix.ru/rest_help/), с тем отличием, что при необходимости можно указать callback 3-им параметром.
