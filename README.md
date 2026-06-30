# MethodBorrowLibrary

Библиотека для заимствования методов из классов.

## Описание

### Основные классы приложения

#### Context (Somethinganotherbeer\Methodborrow\Context)

В данном классе реализуется метод Context::borrowMethodFromClass(string $classname, string $method_name). Посредством которого мы получаем экземпляр класса ContextMethod. Экземпляр данного класса при создании принимает объект конфигурации, который мы рассмотрим позже.

#### ContextMethod (Somethinganotherbeer\Methodborrow\ContextMethod)

Экземпляр данного класса возвращается при успешном выполнении Context::borrowMethodFromClass(string $classname, string $method_name). Реализует метод __invoke с переменным кл-вом аргументов, посредством которого осуществляется проксирование к ReflectionMethod::invoke ранее указанного метода с передачей ему этих аргументов. Имеется возможность использовать методы bind и bindArgs, первый из которых биндит аргументы, которые будут передаваться автоматически при вызове __invoke без их указания в вызове. bind принимает одиночное значение, а bindArgs принимает массив значений. Можно очистить забинженные аргументы посредством вызова clear()

### Конфигурация

Конфигурация представляет собой переменную ассоциативного массива, ключами которого являются полностью квалифицированные имена классов. Каждое значение, привязанное к ключу по имени класса, должно представлять собой массив, содержащий два возможных ключа: implementation_list и replacement_list, каждое из которых представляет собой массив, содержащий массив массивов со следующими возможными значениями для:
1) implementation_list - [...['implementation_name' => {string}, 'implementation_value' => {string}]]
2) replacement_list - [...['replacement_name' => {string}, 'replacement_value' => {mixed}]]

Массив по ключу implementation_list должен содержать значения для реализации. Если в классе, указанном в первом аргументе Context::borrowMethodFromClass(string $classname, string $method_name), в качестве свойства, инициализируемого в конструкторе, используется абстрактный класс или интерфейс. По ключу implementation_name указывается наименование абстрактного класса/интерфейса, а по ключу implementation_value указывается его реализация. При этом, класс, указанный в implementation_value, должен действительно реализовывать абстрактный класс/интерфейс, указанный в implementation_name.

Массив по ключу replacement_list должен содержать значения для встроенных аргументов. Если конструктор указанного класса содержит сигнатуру встроенного типа без дефолтного значения, например int, float и т.д, то мы не можем взять этот параметр из ниоткуда. Его значение необходимо прописать в подмассиве массива replacement_name следующим образом - ['replacement_name' => '{Наименование аргумента}, 'replacement_value' => {значение аргумента}]. Если же конструктору класса передаются значения по умолчанию для встроенного типа, то все ок.

### Пример использования вместе с  конфигурацией

```php

class One
{
    private SomeInterface $innerInstance;
    private Three $three;
    private int $someValue;

    public function __construct(SomeInterface $innerInstance, Three $three, int $someValue)
    {
        $this->innerInstance = $innerInstance;
        $this->three = $three;
        $this->someValue = $someValue;

    }

    public function doSomething(int $arg): int
    {
        return $arg * $arg;
    }
}

interface SomeInterface
{

}

class Two implements SomeInterface
{

}

class Three
{
    private Four $four;

    public function __construct(Four $four)
    {
        $this->four = $four;
    }
}

class Four
{

}

$configuration = 
[
    'One' => 
    [
        'implementation_list' => 
        [
            [
                'implementation_name' => 'SomeInterface',
                'implementation_value' => 'Two'
            ],
        ],
        'replacement_list' =>
        [
            [
                'replacement_name' => 'someValue',
                'replacement_value' => 5,
            ],
        ], 
    ],
];

$confFactory = ConfFactory::makeInstance();
$classConfList = $confFactory->makeClassConfList($configuration);
$context = Context::makeInstance($classConfList);

$method = $context->borrowMethodFromClass(One::class, 'doSomething');

$method(5) // 25

$method->bind(5);
$method() // 25
$method->clear();
$method() // получим ошибку, так как нет забинженных аргументов

```