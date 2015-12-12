<?php

namespace QuickPdo;


/**
 * QuickPdo
 * @author Lingtalfi
 * 2015-09-25
 *
 */
class QuickPdo
{


    /**
     * @var \PDO
     */
    private static $conn;
    private static $stmt;
    /**
     * @var array containing statement/connection errors
     * The format is:
     *      - 0: SQLSTATE error code
     *      - 1: Driver-specific error code
     *      - 2: Driver-specific error message
     *      - 3: this class' method name
     *
     */
    private static $errors = [];

    public static function setConnection($dsn, $user, $pass, array $options)
    {
        self::$conn = new \PDO(
            $dsn,
            $user,
            $pass,
            $options
        );
    }

    /**
     * @return \PDO
     * @throws \Exception
     */
    public static function getConnection()
    {
        if (null === self::$conn) {
            throw new \Exception("Connection not set");
        }
        return self::$conn;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /**
     * @return false|int, last insert id
     * Errors are accessible via a getError method
     *
     * Common errors are:
     * - SQLSTATE[42S22]: Column not found: 1054 Unknown column 'dddescription'
     * - SQLSTATE[42S02]: Base table or view not found: 1146 Table 'calendar.the_ev' doesn't exist
     * - SQLSTATE[HY000]: General error: 1364 Field 'end_date' doesn't have a default value
     *
     *
     */
    public static function insert($table, array $fields)
    {
        $stmt = 'insert into ' . $table . ' set ';
        $first = true;
        $markers = [];
        foreach ($fields as $k => $v) {
            if (true === $first) {
                $first = false;
            }
            else {
                $stmt .= ',';
            }
            $stmt .= $k . '=:' . $k;
            $markers[':' . $k] = $v;
        }


        $pdo = self::getConnection();
        self::$stmt = $stmt;
        $query = $pdo->prepare($stmt);
        if (true === $query->execute($markers)) {
            return $pdo->lastInsertId();
        }
        self::handleStatementErrors($query, 'insert');
        return false;
    }


    /**
     * Returns true|false
     *
     * - whereConds: array of whereCond|glue
     * with:
     *
     * - whereCond:
     * ----- 0: field
     * ----- 1: operator (<, =, >, <=, >=, like, between)
     * ----- 2: operand (the value to compare the field with)
     * ----- ?3: operand 2, only if between operator is used
     *
     *          Note: for mysql users, if the like operator is used, the operand can contain the wildcards chars:
     *
     *          - %: matches any number of characters, even zero characters
     *          - _: matches exactly one character
     *
     *          To use the literal version of a wildcard char, prefix it with backslash (\%, \_).
     *          See mysql docs for more info.
     *
     *
     * - glue: string directly injected in the statement, so that one
     *              can create the logical AND and OR and parenthesis operators.
     *              We can also use it with the IN keyword, for instance:
     *                      - in ( 6, 8, 9 )
     *                      - in ( :doo, :foo, :koo )
     *              In the latter case, we will also pass corresponding markers manually using the $extraMarkers argument.
     *                      doo => 6,
     *                      koo => 'something',
     *                      ...
     *
     *
     *
     *
     * Common errors are:
     * - SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax;
     * - SQLSTATE[42S02]: Base table or view not found: 1146 Table 'calendar.the_ev' doesn't exist
     * - SQLSTATE[42S22]: Column not found: 1054 Unknown column 'dddescription'
     *
     *
     *
     */
    public static function update($table, array $fields, $whereConds, array $extraMarkers = [])
    {

        $pdo = self::getConnection();
        $stmt = 'update ' . $table . ' set ';
        $markers = [];
        $first = true;
        foreach ($fields as $k => $v) {
            if (true === $first) {
                $first = false;
            }
            else {
                $stmt .= ',';
            }
            $stmt .= $k . '=:' . $k;
            $markers[':' . $k] = $v;
        }

        self::addWhereSubStmt($whereConds, $stmt, $markers);
        $markers = array_replace($markers, $extraMarkers);
        self::$stmt = $stmt;
        $query = $pdo->prepare($stmt);
        if (true === $query->execute($markers)) {
            return true;
        }
        self::handleStatementErrors($query, 'update');
        return false;
    }


    /**
     * Returns false|int, the number of deleted rows
     * For whereConds format, see update method.
     *
     * Common errors are:
     * - SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax;
     * - SQLSTATE[42S02]: Base table or view not found: 1146 Table 'calendar.the_ev' doesn't exist
     * - SQLSTATE[42S22]: Column not found: 1054 Unknown column 'dddescription'
     */
    public static function delete($table, array $whereConds = [])
    {

        $pdo = self::getConnection();
        $stmt = 'delete from ' . $table;
        $markers = [];
        self::addWhereSubStmt($whereConds, $stmt, $markers);
        self::$stmt = $stmt;
        $query = $pdo->prepare($stmt);
        if (true === $query->execute($markers)) {
            return $query->rowCount();
        }
        self::handleStatementErrors($query, 'delete');
        return false;
    }


    /**
     * Returns false|array
     *
     * Common errors are:
     * - SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax;
     * - SQLSTATE[42S02]: Base table or view not found: 1146 Table 'calendar.the_ev' doesn't exist
     * - SQLSTATE[42S22]: Column not found: 1054 Unknown column 'dddescription'
     */
    public static function fetchAll($stmt, array $markers = [])
    {
        $pdo = self::getConnection();
        self::$stmt = $stmt;
        $query = $pdo->prepare($stmt);
        if (true === $query->execute($markers)) {
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        }
        self::handleStatementErrors($query, 'fetchAll');
        return false;
    }


    /**
     * Returns false|array
     *
     * Common errors are:
     * - SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax;
     * - SQLSTATE[42S02]: Base table or view not found: 1146 Table 'calendar.the_ev' doesn't exist
     * - SQLSTATE[42S22]: Column not found: 1054 Unknown column 'dddescription'
     */
    public static function fetch($stmt, array $markers = [])
    {
        $pdo = self::getConnection();
        self::$stmt = $stmt;
        $query = $pdo->prepare($stmt);
        if (true === $query->execute($markers)) {
            return $query->fetch(\PDO::FETCH_ASSOC);
        }
        self::handleStatementErrors($query, 'fetch');
        return false;
    }


    /**
     * Executes a PDO->exec and returns the number of affected lines.
     *
     * @return false|int, the number of affected rows
     *
     *
     * Common errors:
     * - SQLSTATE[42000]: Syntax error or access violation: 1049 Unknown database 'pou'
     *
     */
    public static function freeExec($stmt)
    {
        $pdo = self::getConnection();
        self::$stmt = $stmt;
        if (false !== $r = $pdo->exec($stmt)) {
            return $r;
        }
        self::handleConnectionErrors($pdo, 'freeExec');
        return false;
    }


    /**
     * Execute a PDOStatement->execute and returns the number of affected rows.
     *
     *
     * @return false|int, the number of affected rows
     */
    public static function freeStmt($stmt, array $markers = [])
    {
        $pdo = self::getConnection();
        self::$stmt = $stmt;
        $query = $pdo->prepare($stmt);
        if (true === $query->execute($markers)) {
            return $query->rowCount();
        }
        self::handleStatementErrors($query, 'freeStmt');
        return false;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public static function getErrors()
    {
        return self::$errors;
    }

    public static function getLastError()
    {
        return self::$errors[count(self::$errors) - 1];
    }



    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    private static function addWhereSubStmt($whereConds, &$stmt, array &$markers)
    {
        if (is_array($whereConds)) {
            if ($whereConds) {

                $mkCpt = 0;
                $mk = 'bzz_';
                $stmt .= ' where ';
                $first = true;
                foreach ($whereConds as $cond) {
                    if (is_array($cond)) {
                        list($field, $op, $val) = $cond;
                        $val2 = (isset($cond[3])) ? $cond[3] : null;
                        if (true === $first) {
                            $first = false;
                        }
                        else {
                            $stmt .= ' and ';
                        }
                        $stmt .= $field . ' ' . $op . ' :' . $mk . $mkCpt;
                        $markers[':' . $mk . $mkCpt] = $val;
                        $mkCpt++;
                        if ('between' === $op) {
                            $stmt .= ' and ' . ' :' . $mk . $mkCpt;
                            $markers[':' . $mk . $mkCpt] = $val2;
                            $mkCpt++;
                        }
                    }
                    elseif (is_string($cond)) {
                        $stmt .= $cond;
                    }
                }
            }
        }
        elseif (is_string($whereConds)) {
            $stmt .= ' where ' . $whereConds;
        }
    }

    private static function handleStatementErrors(\PDOStatement $query, $methodName)
    {
        if (0 !== (int)$query->errorInfo()[1]) {
            self::$errors[] = array_merge($query->errorInfo(), [$methodName]);
        }
    }

    private static function handleConnectionErrors(\PDO $conn, $methodName)
    {
        if (0 !== (int)$conn->errorInfo()[1]) {
            self::$errors[] = array_merge($conn->errorInfo(), [$methodName]);
        }
    }
}
