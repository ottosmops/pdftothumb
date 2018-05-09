<?php

namespace Ottosmops\Pdftothumb\Exceptions;

use Symfony\Component\Process\Exception\ProcessFailedException;

class CouldNotConvertPdf extends ProcessFailedException
{
}
