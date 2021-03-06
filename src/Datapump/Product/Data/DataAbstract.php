<?php
/**
 * @author Martin Aarhof <martin.aarhof@gmail.com>
 * @version GIT: $Id$
 */
namespace Datapump\Product\Data;

/**
 * Class DataAbstract
 * @package Datapump\Product\Data
 */
abstract class DataAbstract implements DataInterface
{

    /**
     * Set required Magmi plugins
     * @var array
     */
    protected $requiredMagmiPlugin = array();

    /**
     * Data of the product
     * @var array
     */
    protected $data = array();

    /**
     * Get the data for the itemholder
     * @return array
     */
    abstract public function getData();

    /**
     * Set data
     *
     * @param string $key
     * @param mixed $value
     *
     * @return DataAbstract
     */
    public function __set($key, $value)
    {
        // @codeCoverageIgnoreStart
        return $this->set($key, $value);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Set data
     *
     * @param string $key
     * @param mixed $value
     *
     * @return RequiredData
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Get data of the key
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function __get($key)
    {
        // @codeCoverageIgnoreStart
        return $this->get($key);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get data of the key
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        if ($this->__isset($key)) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Check if a key is set in the data
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Unset data
     *
     * @param string $key
     * @return $this
     */
    public function unsetKey($key)
    {
        if ($this->__isset($key)) {
            unset($this->data[$key]);
        }
        return $this;
    }
}
