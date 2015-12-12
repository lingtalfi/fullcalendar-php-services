QuickPdo
=================
2015-10-04





What is it?
-------------------


It's a static class that contains basic methods to interact with a mysql database via pdo.
 
 
What's the benefit?
------------------------

- very small (less than 300 lines of code)
- it automatically creates prepared parameters (sql injection safe) for insert, update, when it makes sense to do so 
- it shortens the mysql query a bit 
 
 
 
 
How to use
---------------
 
First inject your pdo connection at some point in your application:

```php
QuickPdo::setConnection(
    PDOCONF_DSN,
    PDOCONF_USER,
    PDOCONF_PASS,
    array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
        PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    )
);

```
 
 
Then you can use the QuickPdo methods anywhere after.<br>
QuickPdo assumes that the mysql error mode is set to ERRMODE_EXCEPTION, and does not try to handle the errors for you. 







Examples
-------------
  
  
  
### Fetch
```php

$stmt = 'select count(*) as count from mytable where active=6';
if (false !== ($row = QuickPdo::fetch($stmt)) {
    $count = $row['count'];
}

```

  
  
### FetchAll
```php

$stmt = 'select id, the_name, active from ideas where the_name like :name';
$rows = QuickPdo::fetchAll($stmt, [
    'name' => '%'. str_replace('%','\%', $thename) .'%',
]);

```


### Insert
  
```php
// this is a prepared request
if (false !== ($lastId = QuickPdo::insert('mytable', [
        'name' => 'Morris',   
        'age' => 45,
    ]))
) {
    echo $lastId;
}

```


### Delete
   
Showing different forms of [whereConds](https://github.com/lingtalfi/QuickPdo) (second argument)
  
```php

// this form is more compact, but only works safely with ints or trusted data
QuickPdo::delete('superusers', 'the_timestamp > 1000000 and active=1');

// this form is less compact, but uses prepared parameters under the hood (safe even with untrusted strings)  
if (false !== ($n = QuickPdo::delete('superusers', [
        ['the_timestamp', '>', 1000000],
        ['active', '=', 1],
    ]))
) {
    echo "$n entries have been deleted";
}

```

  
### Update 


#### Default case  
  
```php
    
// it updates table mytable and set name to Alice where id = 1
QuickPdo::update('mytable', ['name' => 'Alice'], [
        ['id', '=', 1],
    ]);
    
// Note: like for most of the methods with QuickPdo, this is a prepared query ( which means internally, the following 
// request is executed: 'update mytable set name=:name where id = :bzz_0', and the markers are set accordingly )    

``````

#### Using where with "in" with int values 
  
```php
        
$sValues = '1, 2, 3';        
$rows = QuickPdo::update(
    $currentTable,
    ['active' => $newStatus],
    "id in ($sValues)" // I use this form when I'm sure that sValues contains only ints (or trusted data)
);
``````



#### Using where with "in" with unsafe values 
  
```php
        
        
$rows = QuickPdo::update(
    $currentTable,
    ['active' => $newStatus],
    "name in (:boo, :doo, :coo)",   // I use this form when I don't trust the data
    [
        'boo' => $booValue,
        'doo' => $dooValue,
        'coo' => $cooValue,
    ]
);

``````
  


 
 
The Where notation
----------------------

Whenever you can use a where clause in your request (update, delete),
QuickPdo uses the following special value called whereConds:


    - whereConds: array of whereCond|glue
    
    ----- whereCond:
    --------- 0: field
    --------- 1: operator (<, =, >, <=, >=, like, between)
    --------- 2: operand (the value to compare the field with)
    --------- ?3: operand 2, only if between operator is used
    
                  Note: for mysql users, if the like operator is used, the operand can contain the wildcards chars:
            
                  - %: matches any number of characters, even zero characters
                  - _: matches exactly one character
            
                  To use the literal version of a wildcard char, prefix it with backslash (\%, \_).
                  See mysql docs for more info.
    
    
    ----- glue: string directly injected in the statement, so that one
              can create the logical AND and OR and parenthesis operators.
              We can also use it with the IN keyword, for instance:
                      - in ( 6, 8, 9 )
                      - in ( :doo, :foo, :koo )
              In the latter case, we will also pass corresponding markers manually using the $extraMarkers argument.
                      doo => 6,
                      koo => 'something',
                      ...
    





Methods
---------------


Return     |  Method Name                                       | Comments
---------- | -------------------------------------------------- | ---------------------
void       |     setConnection ( \PDO ) |
\PDO       |     getConnection ()        |                                      // Or throws \Exception
false\|int  |     insert ( table, array fields)                               | // Returns the last inserted id in case of success
bool        |    update ( table, array fields, whereConds, extraMarkers?)    | // Returns true in case of success
false\|int   |    delete ( table, whereConds)                                 | // Returns the number of deleted entries in case of success
false\|array  |   fetchAll ( stmt, array markers?)                            | // Returns the rows in case of success
false\|array   |  fetch ( stmt, array markers?)                               | // Returns a single row in case of success
false\|int     |  freeStmt( stmt, array markers?)                             | // Returns the number of affected lines in case of success
false\|int    |   freeExec( stmt )                                            | // Returns the number of affected lines in case of success
                                                                            // This is not a prepared request, it calls pdo->exec directly




Tips
-----------
  
Access the last executed stmt
```php
    
    // execute a request with QuickPdo...
    echo QuickPdo::$stmt;

```







How to make transaction
--------------------------

QuickPdo doesn't have special methods for transaction, just use transaction as you would normally do


```php
$conn = QuickPdo::getConnection();
try {
    $conn->beginTransaction();
    QuickPdo::update('mytable', ['name' => 'Alice'], [
        ['id', '=', 1],
    ]);
    // ...other stuff
    $conn->commit();

} catch (\Exception $e) {
    $conn->rollBack();
}
```



 
 
Friends
-----------
  
If your intention is to display table results inside terminal, you might want to checkout
the [MysqlTabular Tool](https://github.com/lingtalfi/MysqlTabular).



```php
    
$stmt = <<<EEE
select id, committer_id, the_name, publish_date, active from ideas order by publish_date desc limit 0,10
EEE;

// Here, use any method that you like to generate the rows
$rows = QuickPdo::fetchAll($stmt);


$o  = new MysqlTabularAssocUtil();
echo $o->renderRows($rows);

```

Then the results will look like this on the console:

    
    +----+--------------+-----------+---------------------+--------+
    | id | committer_id | the_name  | publish_date        | active |
    +----+--------------+-----------+---------------------+--------+
    | 68 |           15 | pou       | 2015-10-02 09:29:02 |      0 |
    | 67 |           14 | r         | 2015-10-02 09:22:52 |      0 |
    | 66 |           13 | zezer     | 2015-10-02 07:59:16 |      0 |
    | 65 |           13 | ze        | 2015-10-02 07:58:21 |      0 |
    | 64 |           13 | pjzpeée   | 2015-10-02 07:37:46 |      0 |
    | 63 |           13 | pjzper    | 2015-10-02 07:20:16 |      0 |
    | 62 |           13 | zer       | 2015-10-02 06:59:53 |      0 |
    | 60 |           12 | sdf       | 2015-10-02 06:52:51 |      0 |
    | 59 |           11 | Chun li   | 2015-09-30 14:03:27 |      0 |
    | 58 |           11 | Boris Pan | 2015-09-30 13:50:51 |      0 |
    +----+--------------+-----------+---------------------+--------+


 
 
 
History Log
------------------
    
- 1.1.0 -- 2015-12-11

    - add possibility to retrieve errors manually (to work with PDO_ERRMODE_SILENT for instance)
    
- 1.0.1 -- 2015-11-07

    - fix bug: incorrect where clause
       
- 1.0.0 -- 2015-10-04

    - initial commit
    
     
 
 