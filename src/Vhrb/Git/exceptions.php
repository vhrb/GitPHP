<?php

namespace Vhrb\Git;

interface Exception
{
}

class InvalidArgumentException extends \InvalidArgumentException implements Exception
{
}

class InvalidStateException extends \RuntimeException implements Exception
{
}

class ExecuteCommandException extends \InvalidArgumentException implements Exception
{
}