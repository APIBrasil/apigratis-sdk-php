<?php

namespace ApiBrasil\Test;

use Service\WhatsApp;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    
    protected $client;
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new WhatsApp();
        
    }

    /** @test */
    public function it_should_be_a_client_instance()
    {
        $this->assertInstanceOf(WhatsApp::class, $this->client);
    }
}