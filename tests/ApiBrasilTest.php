<?php

namespace ApiBrasil\Test;

use ApiBrasil\Service;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    
    protected $client;
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Service();
        
    }

    /** @test */
    public function it_should_be_a_client_instance()
    {
        $this->assertInstanceOf(Service::class, $this->client);
    }
}