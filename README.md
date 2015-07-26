# TreeClosureTable

Хранение иерархических древовидных структур в базе данных методом «Closure Table» совмещённым с «Adjacency List».

# Example

http://drandin.ru/projects/TreeClosureTable/example/index.html

<h2>Как использовать TreeClosureTable?</h2>

1. Создать две таблицы в базе данных. 
2. Настроить параметры.

В каталоге example приведён пример использования TreeClosureTable.

Схемы таблиц `comments` and `commentsTree`:

```sql
CREATE TABLE `comments` (
  `idEntry` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `dateCreate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateUpdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idEntry`),
  KEY `idEntry` (`idEntry`,`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 ```

```sql
CREATE TABLE `commentsTree` (
  `idAncestor` int(11) NOT NULL,
  `idDescendant` int(11) NOT NULL,
  `idNearestAncestor` int(11) NOT NULL DEFAULT '0',
  `level` smallint(6) NOT NULL DEFAULT '1',
  `idSubject` int(11) NOT NULL,
  `dateCreate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAncestor`,`idDescendant`),
  KEY `idDescendant` (`idDescendant`),
  KEY `idSubject` (`idSubject`),
  KEY `main` (`idAncestor`,`idDescendant`,`idNearestAncestor`,`level`),
  KEY `idNearestAncestor` (`idNearestAncestor`),
  CONSTRAINT `commentsTree_ibfk_1` 
  FOREIGN KEY (`idAncestor`) 
  REFERENCES `comments` (`idEntry`) 
  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `commentsTree_ibfk_2` 
  FOREIGN KEY (`idDescendant`) 
  REFERENCES `comments` (`idEntry`) 
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

Настройки параметров работы TreeClosureTable и получение объекта класса Commentator для работы с деревом:

```php
$parameters = array(

    /**
     * It is the name of table in DB where stored comments
     */
    'tableData' => 'comments',

    /**
     * It is the name of table in db where stored hierarchic structure of tree
     */
    'tableTree' => 'commentsTree',

    /**
     * Is is name of class, that describes entity 'Comments'
     */
    'entity' => 'TreeClosureTable\Comments',

    /**
     * It is name of field which is ID of comment
     */
    'idTableData' => 'idEntry'
    
);
    
/**
* Object PDO
* $dsn, $user, $password - your parameter for connect DB
*/
$pdo = new PDO($dsn, $user, $password);
    
/**
* Object Commentator
*/    
$commentator = new \TreeClosureTable\Commentator($parameters, $pdo);

```

Получение всех комментарив, относящихся к $idSubject

```php
    $comments = $commentator->setIdSubject($idSubject)->getTree();
```

<h2>Описание методов для работы с деревом</h2>

Check having element in tree
    
```php
    public bool Commentator::hasEntry(int $idEntry);
```

Delete branch of tree
   
```php
    public int Commentator::deleteBranch(int $idEntry);
```

Add one new element into tree
 
```php
    public bool Commentator::add(ClosureTableBase $obj, int $idEntry = 0);
```
Return part of tree or entire hierarchy from root as array
  
```php
    public array Commentator::getArrayTree(int $idEntry = 0);
```

Return part of tree or entire hierarchy from root as object ClosureTableCollection
   
```php
    public ClosureTableCollection Commentator::getDescendants(int $idEntry = 0);
```

Return part of properly constructed tree or entire properly constructed hierarchy from root as object ClosureTableCollection
   
```php
    public ClosureTableCollection|null Commentator::getTree(int $idEntry = 0);
```

Return ID all elements of branch of tree
 
```php
    public array Commentator::getIdEntriesBranch(int $idEntry);
```

Return part tree or entire tree as multidimensional array
    
```php
    public array|null Commentator::getHierarchyArrayTree(int $idEntry = 0);
```

Return level of element
  
```php
    public int Commentator::getLevel($idEntry);
```

Return count of elements in tree which belongs to $idSubject
    
```php
    public int Commentator::countItemsBySubject(int $idSubject);
```

<h2>Author</h2>

[Igor Drandin] (https://github.com/drandin)

<h2>License</h2>

MIT Public License
