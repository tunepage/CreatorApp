# CreatorApp

CreatorApp — небольшой PHPSDK для использования совместно с <a href="https://www.bitrix24.ru/apps/app/tunepage.app_creator/" target="_blank">«Конструктором приложений»</a> для Битрикс24.


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

2. Укажите ключ, который получили через <a href="https://www.bitrix24.ru/apps/app/tunepage.app_creator/" target="_blank">«Конструктор приложений»</a>
```php
$b24CreatorAppKey = '***';
```

3. Укажите ключ member_id портала Битрикс24, к которому нужно отправить запрос. Для примера, если приложение открывается в интерфейсе Битрикс24, то можно указать из переданных данных приложению:
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
echo '<pre>';
print_r(CApp::turn('crm.deal.list', [
    'select' => ['TITLE',]
]));
echo '</pre>';
```


### Отправить запрос на исполнение с обратным вызовом, когда будет готов результат 
```php
// Поставить запрос в очередь c callback
echo '<PRE>';
print_r(CApp::turn(
    'profile', 
    [], //пустой массив, если не нужно указывать параметры
    'https://test.ru/index/'  //адрес для callback
));
echo '</PRE>';
echo '<pre>';
print_r(CApp::turn(
    'crm.deal.list', 
    [
        'select' => ['TITLE',]
    ],
    'https://test.ru/index/'  //адрес для callback
));
echo '</pre>';
```

## Рекомендации

