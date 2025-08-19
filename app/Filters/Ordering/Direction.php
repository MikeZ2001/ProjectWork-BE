<?php

namespace App\Filters\Ordering;

enum Direction: string
{
    case DESC = 'desc';
    case ASC = 'asc';
}