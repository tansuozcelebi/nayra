<?php

namespace ProcessMaker\Nayra\Bpmn;

use ProcessMaker\Nayra\Contracts\Bpmn\ItemDefinitionInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\ProcessInterface;

/**
 * Implementation of the behavior for a data store.
 */
trait DataStoreTrait
{
    use FlowElementTrait;

    private $data = [];

    /**
     * @var \ProcessMaker\Nayra\Contracts\Bpmn\ProcessInterface
     */
    private $process;

    /**
     * @var \ProcessMaker\Nayra\Contracts\Bpmn\ItemDefinitionInterface
     */
    private $itemSubject;

    /**
     * @var int|null
     */
    private $lastSyncTime;

    /**
     * Get owner process.
     *
     * @return ProcessInterface
     */
    public function getOwnerProcess()
    {
        return $this->process;
    }

    /**
     * Get Process of the application.
     *
     * @param \ProcessMaker\Nayra\Contracts\Bpmn\ProcessInterface $process
     *
     * @return ProcessInterface
     */
    public function setOwnerProcess(ProcessInterface $process)
    {
        $this->process = $process;
        $this->getId();

        return $this;
    }

    /**
     * Get data from store.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getData($name = null, $default = null)
    {
        return $name === null ? $this->data : (isset($this->data[$name]) ? $this->data[$name] : $default);
    }

    /**
     * Set data of the store.
     *
     * @param array $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Put data to store.
     *
     * @param string $name
     * @param mixed $data
     *
     * @return $this
     */
    public function putData($name, $data)
    {
        $this->data[$name] = $data;

        return $this;
    }

    /**
     * Get the items that are stored or conveyed by the ItemAwareElement.
     *
     * @return ItemDefinitionInterface
     */
    public function getItemSubject()
    {
        return $this->itemSubject;
    }

    /**
     * Sync data from another data store.
     *
     * @param \ProcessMaker\Nayra\Contracts\Bpmn\DataStoreInterface $source
     *
     * @return $this
     */
    public function syncFrom($source)
    {
        $sourceData = $source->getData();
        if (is_array($sourceData)) {
            $this->data = array_merge($this->data, $sourceData);
            $this->lastSyncTime = time();
        }

        return $this;
    }

    /**
     * Get last sync timestamp.
     *
     * @return int|null
     */
    public function getLastSyncTime()
    {
        return $this->lastSyncTime;
    }
}
