<?php

namespace App\Support;

use Illuminate\Support\Collection;

class CollectionMacros
{
    public static function register(): void
    {
        Collection::macro('weightedRandom', function () {
            $total = $this->sum();
            $random = rand(1, $total);
            
            $sum = 0;
            foreach ($this as $item => $weight) {
                $sum += $weight;
                if ($random <= $sum) {
                    return $item;
                }
            }
            
            return $this->keys()->last();
        });
    }
}
