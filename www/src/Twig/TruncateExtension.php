<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TruncateExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('textTruncate', [$this, 'textTruncate']),
        ];
    }

    public function textTruncate(string $text, int $maxPos = 180)
    {
        if (strlen($text) > $maxPos)
        {
            $lastPos = ($maxPos - 3) - strlen($text);
            return substr($text, 0, strrpos($text, ' ', $lastPos)) . '...';
        }
    }
}
