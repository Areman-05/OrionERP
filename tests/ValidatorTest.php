<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use OrionERP\Utils\Validator;

class ValidatorTest extends TestCase
{
    public function testEmailValido(): void
    {
        $this->assertTrue(Validator::email('test@example.com'));
        $this->assertTrue(Validator::email('usuario.nombre@dominio.co.uk'));
    }

    public function testEmailInvalido(): void
    {
        $this->assertFalse(Validator::email('email-invalido'));
        $this->assertFalse(Validator::email('sin@arroba'));
        $this->assertFalse(Validator::email('@sinusuario.com'));
    }

    public function testRequired(): void
    {
        $this->assertTrue(Validator::required('texto'));
        $this->assertTrue(Validator::required(123));
        $this->assertFalse(Validator::required(''));
        $this->assertFalse(Validator::required(null));
    }

    public function testMinLength(): void
    {
        $this->assertTrue(Validator::minLength('texto', 5));
        $this->assertFalse(Validator::minLength('text', 5));
    }

    public function testNumeric(): void
    {
        $this->assertTrue(Validator::numeric('123'));
        $this->assertTrue(Validator::numeric(123));
        $this->assertTrue(Validator::numeric('123.45'));
        $this->assertFalse(Validator::numeric('abc'));
    }

    public function testDNI(): void
    {
        $this->assertTrue(Validator::dni('12345678A'));
        $this->assertFalse(Validator::dni('1234567A'));
        $this->assertFalse(Validator::dni('123456789'));
    }
}

