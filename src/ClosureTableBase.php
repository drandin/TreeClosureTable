<?php namespace TreeClosureTable;

/**
 * Class ClosureTableBase
 */
abstract class ClosureTableBase
{
    /**
     * Номер записи
     * @var int
     */
    protected $idEntry = 0;

    /**
     * Предок
     * @var int
     */
    protected $idAncestor = 0;

    /**
     * Потомок
     * @var int
     */
    protected $idDescendant = 0;

    /**
     * Ближайший предок
     * @var int
     */
    protected $idNearestAncestor = 0;

    /**
     * Уровень вложености
     * @var int
     */
    protected $level = 0;

    /**
     * Код субъекта
     * @var int
     */
    protected $idSubject = 0;

    /**
     * Код пользователя
     * @var int
     */
    protected $idUser = 0;
    protected $content;
    protected $dateCreate;
    protected $dateUpdate;


    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (property_exists(__CLASS__, $key) && isset($val)) {
                    $method = $this->nameMethodSetter($key);
                    if ($method !== false) {
                        $this->$method($val);
                    }
                }
            }
        }
    }

    /**
     * return name of method $nameProperty
     * @param $nameProperty
     * @return bool|string
     */
    public function nameMethodSetter($nameProperty)
    {
        if (preg_match('/^[a-z]{1}[0-9A-Za-z_]+$/', $nameProperty) === 1) {
            $nameMethod = 'set'.ucfirst($nameProperty);
            if (method_exists($this, $nameMethod)) {
                return $nameMethod;
            }
        }

        return false;
    }

    public function getIdEntry() {
        return (int)$this->idEntry;
    }

    public function getIdAncestor() {
        return (int)$this->idAncestor;
    }

    public function getIdDescendant() {
        return (int)$this->idDescendant;
    }

    public function getIdNearestAncestor() {
        return (int)$this->idNearestAncestor;
    }

    public function getLevel() {
        return (int)$this->level;
    }

    public function getIdSubject() {
        return $this->idSubject;
    }

    public function getIdUser() {
        return (int)$this->idUser;
    }

    public function getContent() {
        return $this->content;
    }

    public function getDateCreate() {
        return $this->dateCreate;
    }

    public function getDateUpdate() {
        return $this->dateUpdate;
    }

    public function setIdEntry($idEntry) {
        if ($idEntry > 0) {
            $this->idEntry = (int)$idEntry;
        }

        return $this;
    }

    public function setIdAncestor($idAncestor) {
        if ($idAncestor >= 0) {
            $this->idAncestor = (int)$idAncestor;
        }

        return $this;
    }

    public function setIdDescendant($idDescendant) {
        if ($idDescendant > 0) {
            $this->idDescendant = (int)$idDescendant;
        }

        return $this;
    }

    public function setIdNearestAncestor($idNearestAncestor) {
        if ($idNearestAncestor >= 0) {
            $this->idNearestAncestor = (int)$idNearestAncestor;
        }

        return $this;
    }

    public function setLevel($level) {
        if ($level > 0) {
            $this->level = (int)$level;
        }

        return $this;
    }

    public function setIdSubject($idSubject) {
        $this->idSubject = (int)$idSubject;
        return $this;
    }

    public function setIdUser($idUser) {
        if ($idUser >= 0) {
            $this->idUser = (int)$idUser;
        }

        return $this;
    }

    public function setContent($content) {
        if (is_string($content)) {
            $this->content = $content;
        }

        return $this;
    }

    public function setDateCreate($dateCreate) {
        if (is_string($dateCreate)) {
            $this->dateCreate = $dateCreate;
        }
    }

    public function setDateUpdate($dateUpdate) {
        if (is_string($dateUpdate)) {
            $this->dateUpdate = $dateUpdate;
        }

        return $this;
    }


}