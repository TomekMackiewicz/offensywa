<?php

namespace FileManagerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FileManagerBundle extends Bundle
{

    public function getParent()
    {
        return 'ArtgrisFileManagerBundle';
    }
    
}
