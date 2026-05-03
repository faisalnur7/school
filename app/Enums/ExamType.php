<?php

namespace App\Enums;

enum ExamType: string
{
    case FINAL = 'final';
    case MIDTERM = 'midterm';
    case QUIZ = 'quiz';
    case ASSIGNMENT = 'assignment';
    case PRACTICAL = 'practical';
}
