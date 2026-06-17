<?php

namespace Butler\Health;

abstract class Check
{
    public string $name;
    public string $slug;
    public string $group;
    public string $description;

    public array $webhooks = [];

    abstract public function run(): Result;
}
