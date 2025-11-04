<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;

class WebrootHelper extends Helper
{
    public function getPath(string $file = ''): string
    {
        return $this->getView()->getRequest()->getAttribute('webroot') . ltrim($file, '/');
    }

    public function webroot(string $file = ''): string
    {
        return $this->getPath($file);
    }

    public function __toString(): string
    {
        return (string)$this->getView()->getRequest()->getAttribute('webroot');
    }
}
