<?php namespace TreeClosureTable;

use TreeClosureTable\Exception\ExceptionClosureTable;

/**
 * Class ClosureTableManagement
 */
class ClosureTableManagement implements IClosureTable
{
    /**
     * @var \PDO
     */
    protected $db;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var array
     */
    protected $arrParameters = array(
        'tableData',
        'tableTree',
        'entity',
        'idTableData'
    );

    /**
     * @var array
     */
    protected $stmt = array();

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $fieldsName = array();

    /**
     * @var null
     */
    protected $idSubject = null;

    /**
     * @var array
     */
    private $treeFlat = array();

    /**
     * @var null|ClosureTableCollection
     */
    private $objTree = null;


    /**
     * @param $parameters
     * @param $db
     * @throws ExceptionClosureTable
     */
    public function __construct($parameters, \PDO $db)
    {
        if (!empty($parameters) && is_array($parameters)) {

            foreach ($this->arrParameters as $item) {

                if (!empty($parameters[$item])) {
                    $this->parameters[$item] = $parameters[$item];
                }
                else {
                    throw new ExceptionClosureTable("Parameter {$item} was specified wrong!");
                }
            }

            if (!class_exists($this->parameters['entity'])) {
                throw new ExceptionClosureTable("Class {$this->parameters['entity']} is not exist!");
            }

            $this->db = $db;
        }
    }


    /**
     * @return int
     */
    public function getIdSubject() {
        return (int)$this->idSubject;
    }

    /**
     * @param $idSubject
     * @return $this
     */
    public function setIdSubject($idSubject) {
        if ($idSubject > 0) {
            $this->idSubject = (int)$idSubject;
        }
        else {
            throw new ExceptionClosureTable("Parameter 'idSubject' was specified wrong!");
        }

        return $this;
    }

    /**
     * Задаёт параметры полей таблицы в массив $this->fields
     * @param $table
     * @return $this
     */
    protected function getFields($table)
    {
        if (!empty($table) && is_string($table) && empty($this->fields[$table])) {
            if (!isset($this->stmt['showColumns'])) {
                $this->stmt['showColumns'] = $this->db->prepare("SHOW COLUMNS FROM `{$table}`");
            }

            $sth = $this->stmt['showColumns'];

            if ($sth instanceof \PDOStatement) {
                if ($sth->execute()) {
                    if ($fields = $sth->fetchAll(\PDO::FETCH_ASSOC)) {
                        $this->fields[$table] = $fields;
                    }
                }
            }
        }

        return $this;
    }


    /**
     * Возвращает массив имён полей таблицы
     * @param $table
     * @return array
     */
    protected function getFieldsName($table)
    {
        if (!empty($table) && empty($this->fieldsName[$table])) {
            foreach ($this->fields[$table] as $item) {
                $this->fieldsName[$table][] = $item['Field'];
            }
        }

        return !empty($this->fieldsName[$table])
            ?  $this->fieldsName[$table]
            : array();
    }

    /**
     * Возвращает имя метода для получения свойства $nameProperty
     * @param $nameProperty
     * @param $obj
     * @return bool|string
     */
    private function nameMethodGetter($nameProperty, $obj)
    {
        if (preg_match('/^[a-z]{1}[0-9A-Za-z_]+$/', $nameProperty)) {
            $nameMethod = 'get'.ucfirst($nameProperty);
            if (method_exists($obj, $nameMethod)) {
                return $nameMethod;
            }
        }

        return false;
    }

    /**
     * @param $nameProperty
     * @param $obj ClosureTableBase
     * @return bool
     */
    private function getProperty($nameProperty, ClosureTableBase $obj)
    {
        $nameMethod = $this->nameMethodGetter($nameProperty, $obj);

        return ($nameMethod !== false)
            ? $obj->$nameMethod()
            : null;
    }


    /**
     * @param $idEntry
     * @return bool
     */
    public function hasEntry($idEntry)
    {
        if ($idEntry > 0) {
            if (empty($this->stmt['selectHasEntry'])) {
                $sql = "SELECT COUNT(*)
                          FROM {$this->parameters['tableTree']}
                         WHERE idDescendant = ?";

                $this->stmt['selectHasEntry'] = $this->db->prepare($sql);
            }

            $stmt = $this->stmt['selectHasEntry'];

            if ($stmt instanceof \PDOStatement) {
                if ($stmt->execute(array((int)$idEntry))) {
                    $data = $stmt->fetch(\PDO::FETCH_NUM);
                    return !empty($data[0]);
                }
            }
        }

        return false;
    }

    /**
     * @param $idEntry
     * @return int
     */
    public function deleteBranch($idEntry)
    {
        if ($idEntry > 0 && $this->db->beginTransaction()) {

            $idEntriesBranch = $this->getIdEntriesBranch($idEntry);

            $countEntries = sizeof($idEntriesBranch);

            if ($countEntries > 0) {

                $placeholders = rtrim(str_repeat('?, ', $countEntries), ', ');

                $sql = "DELETE FROM {$this->parameters['tableTree']}
                              WHERE idDescendant IN ({$placeholders});";

                $stmt = $this->db->prepare($sql);

                if ($stmt->execute($idEntriesBranch) && $stmt->rowCount() > 0) {

                    $sql = "DELETE FROM {$this->parameters['tableData']}
                                  WHERE idEntry IN ({$placeholders});";

                    $stmt = $this->db->prepare($sql);

                    if ($stmt->execute($idEntriesBranch)) {
                        $countDel = $stmt->rowCount();
                        if ($countDel > 0) {
                            if ($this->db->commit()) return $countDel;
                        }
                    }
                }
            }

            $this->db->rollBack();
        }

        return 0;
    }

    /**
     * @param $idEntry
     * @return array
     */
    public function getIdEntriesBranch($idEntry)
    {
        if ($idEntry > 0) {
            if (empty($this->stmt['selectIdDescendants'])) {

                $sql = "SELECT idDescendant AS idEntry
                          FROM {$this->parameters['tableTree']}
                         WHERE idAncestor = :idEntry";

                $this->stmt['selectIdDescendants'] = $this->db->prepare($sql);
            }

            $stmt = $this->stmt['selectIdDescendants'];

            if ($stmt instanceof \PDOStatement) {
                $stmt->bindValue(':idEntry', $idEntry, \PDO::PARAM_INT);
                if ($stmt->execute()) {
                    while ($item = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                        $data[] = (int)$item['idEntry'];
                    }
                }
            }
        }

        return !empty($data) ? $data : array();
    }


    /**
     * @param int $idEntry
     * @param ClosureTableCollection $objCollection
     * @return array|ClosureTableCollection
     */
    protected function getDataTree($idEntry = 0, ClosureTableCollection $objCollection = null)
    {
        if ($idEntry >= 0) {

            $fieldsTableData = $this
                ->getFields($this->parameters['tableData'])
                ->getFieldsName($this->parameters['tableData']);

            if ($idEntry > 0) {
                $data[] = (int)$idEntry;
                $where[] = "tableTree.idAncestor = ?";
            }

            if ($this->idSubject !== null) {
                $where[] = "tableTree.idSubject  = ?";
                $data[] = (int)$this->idSubject;
            }

            if (!empty($where) && !empty($data)) {
                $sql = "SELECT tableData." . implode(", tableData.", $fieldsTableData) . ",
                           tableData.{$this->parameters['idTableData']} AS idEntry,
                           tableTree.idAncestor,
                           tableTree.idDescendant,
                           tableTree.idNearestAncestor,
                           tableTree.level,
                           tableTree.idSubject
                      FROM {$this->parameters['tableData']} AS tableData
                      JOIN {$this->parameters['tableTree']} AS tableTree
                        ON tableData.{$this->parameters['idTableData']} = tableTree.idDescendant
                     WHERE " . implode(' AND ', $where) . "
                  ORDER BY tableData.{$this->parameters['idTableData']} ASC";

                $stmt = $this->db->prepare($sql);

                if ($stmt->execute($data)) {
                    while ($item = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                        if (is_null($objCollection)) $treeData[$item['idEntry']] = $item;
                        else $objCollection->addItem($this->getObject($item));
                    }
                }
            }
        }

        if (is_null($objCollection)) {
            return empty($treeData) ? array() : $treeData;
        }
        else {
            return $objCollection;
        }
    }


    /**
     * Возвращает один элемент дерева
     * @param $idEntry
     * @return array
     */
    protected function getDataOneItem($idEntry)
    {
        if ($idEntry > 0) {

            $fieldsTableData = $this
                ->getFields($this->parameters['tableData'])
                ->getFieldsName($this->parameters['tableData']);

            if ($idEntry > 0) {
                $data[] = (int)$idEntry;
                $where[] = "tableData.{$this->parameters['idTableData']} = ?";
            }

            if ($this->idSubject !== null) {
                $where[] = "tableTree.idSubject  = ?";
                $data[] = (int)$this->idSubject;
            }

            if (!empty($where) && !empty($data)) {
                $sql = "SELECT tableData." . implode(", tableData.", $fieldsTableData) . ",
                           tableData.{$this->parameters['idTableData']} AS idEntry,
                           tableTree.idAncestor,
                           tableTree.idDescendant,
                           tableTree.idNearestAncestor,
                           tableTree.level,
                           tableTree.idSubject
                      FROM {$this->parameters['tableData']} AS tableData
                      JOIN {$this->parameters['tableTree']} AS tableTree
                        ON tableData.{$this->parameters['idTableData']} = tableTree.idDescendant
                     WHERE tableTree.idDescendant = tableTree.idAncestor
                       AND " . implode(' AND ', $where);

                $stmt = $this->db->prepare($sql);

                if ($stmt->execute($data)) {
                    $item = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if (!empty($item)) return $item;

                }
            }
        }

        return array();
    }

    /**
     * @param $idEntry
     * @return ClosureTableCollection
     */
    public function getDescendants($idEntry = 0)
    {
        return $this->getDataTree($idEntry, new ClosureTableCollection());
    }

    /**
     * @param int $idEntry
     * @return array
     */
    public function getArrayTree($idEntry = 0)
    {
        return $this->getDataTree($idEntry);
    }

    /**
     * @param int $idEntry
     * @return ClosureTableCollection|null
     */
    public function getTree($idEntry = 0)
    {
        $this->objTree = null;
        $this->treeFlat = array();
        $this->buildTreeFlat($this->getDataTree($idEntry));
        return $this->objTree;
    }

    /**
     * @param $idEntry
     * @return ClosureTableBase|null
     */
    public function getOneItem($idEntry)
    {
        return $this->getObject($this->getDataOneItem($idEntry));
    }

    /**
     * @param int $idEntry
     * @return array|null
     */
    public function getHierarchyArrayTree($idEntry = 0)
    {
        return $this->buildHierarchyArrayTree($this->getDataTree($idEntry));
    }


    /**
     * @param $treeData
     * @param int $idAncestor
     * @return array|null
     */
    private function buildHierarchyArrayTree($treeData, $idAncestor = 0)
    {
        $tree = array();

        if (is_int($idAncestor) && $idAncestor >= 0) {
            foreach ($treeData as $item) {
                if ((int)$item['idNearestAncestor'] === (int)$idAncestor) {
                    $tree[] = array(
                        'idEntry' => (int)$item['idEntry'],
                        'data' => $treeData[(int)$item['idEntry']],
                        'descendant' => $this->buildHierarchyArrayTree($treeData, (int)$item['idEntry'])
                    );
                }
            }
        }

        return sizeof($tree) === 0
            ? null
            : $tree;
    }


    /**
     * @param $treeData
     * @param int $idAncestor
     * @return array|null
     */
    private function buildTreeFlat($treeData, $idAncestor = 0)
    {
        if (is_int($idAncestor) && $idAncestor >= 0) {

            if (!is_object($this->objTree)) {
                $this->objTree = new ClosureTableCollection();
            }

            foreach ($treeData as $item) {

                if ((int)$item['idNearestAncestor'] === (int)$idAncestor || $this->objTree->count() == 0) {

                    $obj = $this->getObject($treeData[(int)$item['idEntry']]);

                    if ($obj instanceof ClosureTableBase) {
                        $this->objTree->addItem($obj);
                    }

                    $this->buildTreeFlat($treeData, (int)$item['idEntry']);
                }
            }
        }

        return sizeof($this->treeFlat) === 0 ? null : $this->treeFlat;
    }


    /**
     * @param $item
     * @return null|ClosureTableBase
     */
    private function getObject($item)
    {
        if (!empty($item)) {
            $class = $this->parameters['entity'];
            return new $class($item);
        }

        return null;
    }

    /**
     * @param $idEntry
     * @return int
     */
    public function getLevel($idEntry)
    {
        if ($idEntry > 0) {
            if (empty($this->stmt['selectLevel'])) {

                $sql = "SELECT level
                          FROM " . $this->parameters['tableTree'] . "
                         WHERE idAncestor = idDescendant
                           AND idDescendant = ?";

                $this->stmt['selectLevel'] = $this->db->prepare($sql);
            }

            $stmt = $this->stmt['selectLevel'];

            if ($stmt instanceof \PDOStatement) {
                if ($stmt->execute(array((int)$idEntry))) {
                    $data = $stmt->fetch(\PDO::FETCH_NUM);
                    if (isset($data[0])) return (int)$data[0];
                }
            }
        }

        return 0;
    }

    /**
     * @param $idSubject
     * @return int
     */
    public function countItemsBySubject($idSubject)
    {
        if ($idSubject > 0) {

            $sql = "SELECT COUNT(*)
                      FROM {$this->parameters['tableTree']} AS tableTree
                     WHERE tableTree.idSubject = :idSubject";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':idSubject', $idSubject, \PDO::PARAM_INT);

            if ($stmt->execute()) {
                $data = $stmt->fetch(\PDO::FETCH_NUM);
                if (isset($data[0])) return $data[0];
            }
        }

        return 0;
    }

    /**
     * @param ClosureTableBase $obj
     * @param int $idEntry
     * @return bool
     */
    public function add(ClosureTableBase $obj, $idEntry = 0)
    {
        if ($idEntry >= 0) {

            $idEntry = $this->hasEntry($idEntry)
                ? (int)$idEntry
                : 0;

            $tableData = $this->parameters['tableData'];
            $tableTree = $this->parameters['tableTree'];
            $fieldsTableData = $this->getFields($tableData)->getFieldsName($tableData);

            $fields = $values = array();

            foreach ($fieldsTableData as $field) {
                $value = $this->getProperty($field, $obj);
                if (!empty($value)) {
                    list($fields[], $values[]) = array($field, $value);
                }
            }

            if (sizeof($values) > 0 && $this->db->beginTransaction()) {

                $level = $idEntry > 0
                    ? $this->getLevel($idEntry) + 1
                    : 0;

                $sql = "INSERT INTO {$tableData} (" . implode(", ", $fields) . ")
                         VALUES (" . rtrim(str_repeat('?, ', sizeof($fields)), ', ') . ");";

                $sth = $this->db->prepare($sql);

                if ($sth->execute($values)) {

                    $idNewEntry = (int)$this->db->lastInsertId();

                    if ($idNewEntry > 0) {

                        $idSubject = $obj->getIdSubject();

                        $sql = "INSERT INTO {$tableTree} (idAncestor, idDescendant, idNearestAncestor, idSubject, level)
                                 SELECT idAncestor, {$idNewEntry}, {$idEntry}, {$idSubject}, {$level}
                                   FROM {$tableTree}
                                  WHERE idDescendant = {$idEntry}
                              UNION ALL SELECT {$idNewEntry}, {$idNewEntry}, {$idEntry}, {$idSubject}, {$level}";

                       if ($this->db->exec($sql) > 0) {
                           return $this->db->commit();
                       }
                    }
                }
            }

            $this->db->rollBack();
        }

        return false;
    }



}