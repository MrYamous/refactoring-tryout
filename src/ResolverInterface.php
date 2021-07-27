<?php


namespace App;


interface ResolverInterface
{

    public function canResolve(string $text);

    public function resolvePlaceholder(string $text, array $data);

}