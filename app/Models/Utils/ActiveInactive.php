<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models\Utils;

/**
 *
 * @author aleksa
 */
interface ActiveInactive
{
    const ACTIVE   = 1;
    const INACTIVE = 0;
    
    public function isActive(): bool;
    public function isInactive(): bool;
    public function scopeActive($query);
    public function scopeInactive($query);
}
