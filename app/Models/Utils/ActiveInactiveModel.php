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
trait ActiveInactiveModel
{
    protected $activeField = 'active';
    
    public function isActive(): bool
    {
        return $this->{$this->activeField} == self::ACTIVE;
    }
    
    public function isInactive(): bool
    {
        return $this->{$this->activeField} == self::INACTIVE;
    }
    
    public function scopeActive($query)
    {
        $query->where(
            $this->getTable() . '.' . $this->activeField, 
            self::ACTIVE
        );
    }
    
    public function scopeInactive($query)
    {
        $query->where(
            $this->getTable() . '.' . $this->activeField, 
            self::INACTIVE
        );
    }
}
