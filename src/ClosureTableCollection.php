<?php namespace TreeClosureTable;

/**
 * Class ClosureTableCollection
 */
class ClosureTableCollection implements \Iterator
{
    /**
     * @var array
     */
    private $users = array();

    /**
     * @param null $items
     */
    public function __construct($items = null)
    {
        if (is_array($items)) {
            foreach ($items as $item) {
                if ($item instanceof ClosureTableBase) {
                    $this->addItem($item);
                }
            }

            $this->rewind();
        }
    }

    /**
     * @param ClosureTableBase $item
     * @return $this
     */
    public function addItem(ClosureTableBase $item)
    {
        if (!empty($item)) {
            $this->items[] = $item;
            $this->users[$item->getIdUser()] = $item->getIdUser();
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        return array_values($this->users);
    }

    /**
     * @var array
     */
    protected $items = array();


    /**
     * Перемотка в начало
     */
    public function rewind()
    {
        reset($this->items);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * @return mixed|void
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $key = key($this->items);
        return ($key !== null && $key !== false);
    }

    /**
     * @return int
     */
    public function quantity()
    {
        return sizeof($this->items);
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->quantity();
    }
}