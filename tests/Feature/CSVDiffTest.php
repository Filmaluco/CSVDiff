<?php

namespace Tests\Feature;

use App\Libraries\CSVDiff\CSVDiff;
use App\Libraries\CSVDiff\DiffEnum;
use Exception;
use Tests\TestCase;

class CSVDiffTest extends TestCase
{
    /**
     * A basic test to check if the json is generated with the keys :Line: and :Status:
     */
    public function testCSVDiffJson()
    {
        $mockFile1 = storage_path('framework/testing/sample.csv');
        $mockFile2 = storage_path('framework/testing/sample2.csv');

        try {
        $diff = CSVDiff::getDiffFromFiles($mockFile1, $mockFile2);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        
        $this->assertNotEmpty($diff);

        $json = CSVDiff::getJsonFromDiff($diff);
        $data = json_decode($json);

        $this->assertArrayHasKey('0', $data);

        $this->assertNotEmpty($data[0]->Line);
        $this->assertNotEmpty($data[0]->Status);
    }

    /**
     * A basic test to validate the updated status on a mock file designed to test the diff algoritm logic
     */
    public function testCSVUpdatedStatus()
    {
        $mockFile1 = storage_path('framework/testing/sample.csv');
        $mockFile2 = storage_path('framework/testing/sample2.csv');

        try {
        $diff = CSVDiff::getDiffFromFiles($mockFile1, $mockFile2);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        
        $this->assertNotEmpty($diff);

        $json = CSVDiff::getJsonFromDiff($diff);
        $data = json_decode($json);

        $this->assertArrayHasKey('0', $data);

        $this->assertEquals($data[0]->Status, DiffEnum::UPDATED);
    }

    /**
     * A basic test to validate the removed status on a mock file designed to test the diff algoritm logic
     */
    public function testCSVRemovedStatus()
    {
        $mockFile1 = storage_path('framework/testing/sample.csv');
        $mockFile2 = storage_path('framework/testing/sample2.csv');

        try {
        $diff = CSVDiff::getDiffFromFiles($mockFile1, $mockFile2);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        
        $this->assertNotEmpty($diff);

        $json = CSVDiff::getJsonFromDiff($diff);
        $data = json_decode($json);

        $this->assertArrayHasKey('0', $data);

        $this->assertEquals($data[1]->Status, DiffEnum::REMOVED);
    }

    /**
     * A basic test to validate the added status on a mock file designed to test the diff algoritm logic
     */
    public function testCSVAddedStatus()
    {
        $mockFile1 = storage_path('framework/testing/sample.csv');
        $mockFile2 = storage_path('framework/testing/sample2.csv');

        try {
        $diff = CSVDiff::getDiffFromFiles($mockFile1, $mockFile2);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        
        $this->assertNotEmpty($diff);

        $json = CSVDiff::getJsonFromDiff($diff);
        $data = json_decode($json);

        $this->assertArrayHasKey('0', $data);

        $this->assertEquals($data[2]->Status, DiffEnum::ADDED);
    }

    /**
     * A basic test to validate the unchanged status on a mock file designed to test the diff algoritm logic
     */
    public function testCSVUnchagedStatus()
    {
        $mockFile1 = storage_path('framework/testing/sample.csv');
        $mockFile2 = storage_path('framework/testing/sample2.csv');

        try {
        $diff = CSVDiff::getDiffFromFiles($mockFile1, $mockFile2);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        
        $this->assertNotEmpty($diff);

        $json = CSVDiff::getJsonFromDiff($diff);
        $data = json_decode($json);

        $this->assertArrayHasKey('0', $data);

        $this->assertEquals($data[3]->Status, DiffEnum::UNCHANGED);
    }
}
