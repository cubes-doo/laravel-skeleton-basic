<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Lib\HelperPack;


/**
 * @group helper_pack_test
 * 
 * Test HelperPack class 
 */
class HelperPackTest extends TestCase
{

    public function validEmailDataProvider()
    {
        return [
                ["cubes.rs", ".", "Marko", "Pantelic", "marko.pantelic@cubes.rs"],
                ["cubes.rs", ".", "Aleksandar", "Dimic", "aleksandar.dimic@cubes.rs"],
                ["cubes.rs", ".", "Mladen", "Dragonjic", "mladen.dragonjic@cubes.rs"],
                ["gmail.com", "_", "Ronaldo", "Luis", "Nazario", "De", "Lima", 
                 "ronaldo_luis_nazario_de_lima@gmail.com"]
            ];
    }
    

    public function invalidArgsEmailDataProvider()
    {
        return [
                ["cubes.rs", 1, "Marko", "Pantelic", \InvalidArgumentException::class],
                [2, ".", "Aleksandar", "Dimic", \InvalidArgumentException::class],
                ["cubes.rs", ".", 3, "Dragonjic", \InvalidArgumentException::class],
                ["cubes.rs", ".", "Mladen", 4, \InvalidArgumentException::class],
            ];
    }
    
    
    /**
     * 
     * @test
     * @dataProvider validEmailDataProvider
     */
    public function test_generateStr_method($domain, $sep, ...$rest)
    {
        $expected = array_pop($rest);
        $userData = $rest;
        
        $this->assertSame($expected, HelperPack::generateEmailStr($domain, $sep, ...$userData));
    }
    
    
    /**
     * 
     * @test
     * @dataProvider invalidArgsEmailDataProvider
     */
    public function test_generateStr_method_throws_exception_on_invalid_arguments($domain, $sep, ...$rest)
    {
        $expectedExceptionCls = array_pop($rest);
        $userData = $rest;
        
        $this->expectException($expectedExceptionCls);
        
        HelperPack::generateEmailStr($domain, $sep, ...$userData);
    }
}
