<?php

declare(strict_types = 1);

/*
 * This file is part of the Base85 package
 *
 * Copyright (c) 2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/base85
 *
 */

use Tuupola\Base85\PhpEncoder;
use Tuupola\Base85\GmpEncoder;

/**
 * @BeforeMethods({"init"})
 * @Iterations(5)
 * @Warmup(2)
 * @OutputTimeUnit("seconds")
 * @OutputMode("throughput")
 */

class Base85Bench
{
    private $data;
    private $gmp;
    private $php;
    private $encoded;

    public function init()
    {
        $this->data = random_bytes(128);
        $this->gmp = new GmpEncoder;
        $this->php = new PhpEncoder;
        $this->encoded = $this->php->encode($this->data);
    }

    /**
     * @Revs(1000)
     * @Groups({"encoder"})
     */
    public function benchGmpEncoder()
    {
        $encoded = $this->gmp->encode($this->data);
    }

    /**
     * @Revs(1000)
     * @Groups({"encoder"})
     */
    public function benchPhpEncoder()
    {
        $encoded = $this->php->encode($this->data);
    }

    /**
     * @Revs(1000)
     * @Groups({"decoder"})
     */
    public function benchGmpDecoder()
    {
        $decoded = $this->gmp->decode($this->encoded);
    }

    /**
     * @Revs(1000)
     * @Groups({"decoder"})
     */
    public function benchPhpDecoder()
    {
        $decoded = $this->php->decode($this->encoded);
    }
}