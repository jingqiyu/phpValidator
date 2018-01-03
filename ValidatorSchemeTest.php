<?php

final class ValidatorSchemeTest extends PHPUnit_Framework_TestCase {
    public function testTypeCheck() {
        $scheme = new ValidatorScheme("typeCheck","int");
        $err_msg = "";
        $this->assertEquals(
            true,
            $scheme->typeCheck(123,$err_msg)
        );
    }

}