<?php

namespace AccessGrid\Exceptions;

/**
 * Thrown when AES-GCM auth-tag verification fails while decrypting a
 * SmartTap reveal envelope (wrong key, tampered envelope, or wire-format
 * drift between server and SDK).
 */
class DecryptException extends AccessGridException
{
    //
}
