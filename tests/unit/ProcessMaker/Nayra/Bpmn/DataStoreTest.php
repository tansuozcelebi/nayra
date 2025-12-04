<?php

namespace ProcessMaker\Nayra\Bpmn;

use ProcessMaker\Nayra\Contracts\Bpmn\ActivityInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\DataStoreInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\ProcessInterface;
use ProcessMaker\Nayra\Contracts\Bpmn\StateInterface;
use Tests\Feature\Engine\EngineTestCase;

/**
 * Tests for the DataStore class
 */
class DataStoreTest extends EngineTestCase
{
    /**
     * Tests that setters and getters are working properly
     */
    public function testDataStoreSettersAndGetters()
    {
        // Create the objects that will be set in the data store
        $dataStore = $this->repository->createDataStore();
        $process = $this->repository->createProcess();
        $process->setRepository($this->repository);
        $dummyActivity = $this->repository->createActivity();
        $dummyActivity->setRepository($this->repository);
        $state = $this->repository->createState($dummyActivity, '');

        // Set process and state object to the data store
        $dataStore->setOwnerProcess($process);

        //Assertion: The get process must be equal to the set process
        $this->assertEquals($process, $dataStore->getOwnerProcess());

        //Assertion: the data store should have a non initialized item subject
        $this->assertNull($dataStore->getItemSubject());
    }

    /**
     * Tests that syncFrom method works correctly
     */
    public function testDataStoreSyncFrom()
    {
        // Create two data stores
        $sourceStore = $this->repository->createDataStore();
        $targetStore = $this->repository->createDataStore();

        // Set some data in the source store
        $sourceStore->setData(['key1' => 'value1', 'key2' => 'value2']);

        // Set some data in the target store
        $targetStore->setData(['key3' => 'value3']);

        // Sync from source to target
        $targetStore->syncFrom($sourceStore);

        // Assertion: The target store should have data from both stores
        $this->assertEquals('value1', $targetStore->getData('key1'));
        $this->assertEquals('value2', $targetStore->getData('key2'));
        $this->assertEquals('value3', $targetStore->getData('key3'));

        // Assertion: The last sync time should be set
        $this->assertNotNull($targetStore->getLastSyncTime());
        $this->assertIsInt($targetStore->getLastSyncTime());
    }

    /**
     * Tests that syncFrom merges data correctly (source data takes precedence)
     */
    public function testDataStoreSyncFromMergeConflict()
    {
        // Create two data stores
        $sourceStore = $this->repository->createDataStore();
        $targetStore = $this->repository->createDataStore();

        // Set overlapping data
        $sourceStore->setData(['key1' => 'sourceValue', 'key2' => 'value2']);
        $targetStore->setData(['key1' => 'targetValue']);

        // Sync from source to target
        $targetStore->syncFrom($sourceStore);

        // Assertion: Source data should override target data
        $this->assertEquals('sourceValue', $targetStore->getData('key1'));
        $this->assertEquals('value2', $targetStore->getData('key2'));
    }
}
