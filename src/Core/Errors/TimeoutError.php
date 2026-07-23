<?php

declare(strict_types=1);

namespace ApiBrasil\Core\Errors;

/** Timeout — a requisição pode ter sido processada; a SDK não faz retry automático. */
class TimeoutError extends NetworkError
{
}
