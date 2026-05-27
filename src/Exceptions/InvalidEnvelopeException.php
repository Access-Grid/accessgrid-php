<?php

namespace AccessGrid\Exceptions;

/**
 * Thrown when a SmartTap reveal envelope is missing required fields,
 * contains non-base64 / non-PEM data, or otherwise can't be parsed
 * before the cryptographic operations begin.
 */
class InvalidEnvelopeException extends AccessGridException
{
    //
}
