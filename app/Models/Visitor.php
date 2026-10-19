<?php

namespace App\Models;

use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Pengunjung yang masuk lewat Google untuk mengirim pesan kontak.
 *
 * Model ini memakai guard "visitor" sendiri supaya pengunjung tidak
 * pernah dianggap sebagai admin dan tidak bisa membuka /admin.
 */
#[Fillable(['google_id', 'name', 'email', 'avatar'])]
#[Hidden(['remember_token'])]
class Visitor extends Authenticatable
{
    /** @use HasFactory<VisitorFactory> */
    use HasFactory;
}
