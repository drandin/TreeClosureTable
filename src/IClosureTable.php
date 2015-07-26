<?php namespace TreeClosureTable;

/**
 * Interface IClosureTable
 */
interface IClosureTable {

    /**
     * Check having element in tree
     * @param $idEntry
     * @return bool
     */
    public function hasEntry($idEntry);

    /**
     * Delete branch of tree
     * @param $idEntry
     * @return int
     */
    public function deleteBranch($idEntry);

    /**
     * Add one new element into tree
     * @param ClosureTableBase $obj
     * @param int $idEntry
     * @return bool
     */
    public function add(ClosureTableBase $obj, $idEntry = 0);

    /**
     * Return part of tree or entire hierarchy from root as array
     * @param int $idEntry
     * @return array
     */
    public function getArrayTree($idEntry = 0);

    /**
     * Return part of tree or entire hierarchy from root as object ClosureTableCollection
     * @param $idEntry
     * @return ClosureTableCollection
     */
    public function getDescendants($idEntry = 0);

    /**
     * Return part of properly constructed tree or entire properly constructed hierarchy
     * from root as object ClosureTableCollection
     * @param int $idEntry
     * @return ClosureTableCollection|null
     */
    public function getTree($idEntry = 0);

    /**
     * Return ID all elements of branch of tree
     * @param $idEntry
     * @return array
     */
    public function getIdEntriesBranch($idEntry);

    /**
     * Return part tree or entire tree as multidimensional array
     * @param int $idEntry
     * @return array|null
     */
    public function getHierarchyArrayTree($idEntry = 0);

    /**
     * Return level of element
     * @param $idEntry
     * @return int
     */
    public function getLevel($idEntry);

    /**
     * Return count of elements in tree which belongs to $idSubject
     * @param $idSubject
     * @return int
     */
    public function countItemsBySubject($idSubject);

}