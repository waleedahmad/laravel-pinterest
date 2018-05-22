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

class Pin extends Model {
        
    /**
     * The available object keys
     * 
     * @var array
     */
    protected $fillable = ["id", "link", "url", "creator", "board", "created_at", "note", "color", "counts", "media", "attribution", "image", "metadata", "original_link"];

}
