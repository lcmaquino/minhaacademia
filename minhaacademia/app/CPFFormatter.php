<?php

namespace App;

class CPFFormatter
{
    protected $cpf;

    /**
     * Constructor for CPFFormatter class.
     *
     * @param string $cpf_digits
     */
    public function __construct($cpf_digits) {
        $this->cpf = $cpf_digits;
    }

    /**
     * Show CPF like ***.000.000-**
     *
     * @return string
     */
    public function mask()
    {
        $cpf_mask = (strlen($this->cpf) != 11) ? null : '***.' . substr($this->cpf, 3, 3) . '.' . substr($this->cpf, 6, 3) . '-**';
        return $cpf_mask;
    }

    /**
     * Show CPF like 000.000.000-00
     *
     * @return string
     */
    public function show()
    {
        $cpf_show = (strlen($this->cpf) != 11) ? null : substr($this->cpf, 0, 3) . '.' . substr($this->cpf, 3, 3) . '.' . substr($this->cpf, 6, 3) . '-' .substr($this->cpf, -2);
        return $cpf_show;
    }
}
