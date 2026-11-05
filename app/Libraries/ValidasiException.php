<?php

namespace App\Libraries;

use RuntimeException;

/** Dilempar saat data melanggar aturan (mis. nama dobel / pembayaran ganda). */
class ValidasiException extends RuntimeException
{
}
