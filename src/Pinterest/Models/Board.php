<?php
/**
 * Copyright 2015 Waleed Ahmad
 *
 * (c) Waleed Ahmad <waleedgplus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WaleedAhmad\Pinterest\Models;

class Board extends Model {

    /**
     * The available object keys
     *
     * @var array
     */
    protected $fillable = ["id", "name", "url", "description", "creator", "created_at", "counts", "image"];

}