<?php

namespace App\Validator\Document;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class CNPJ extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $patternMessage = 'The CNPJ "{{ value }}" must be `xx.xxx.xxx/xxxx-xx` format.';
    public string $message = 'The CPF "{{ value }}" is not valid.';
}
