<?php
/*
This file is part of SeAT

Copyright (C) 2015 to 2020  Leon Jacobs

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

namespace tehraven\Seat\CorporationOnlyCommands;

use Seat\Services\AbstractSeatPlugin;
use tehraven\Seat\CorporationOnlyCommands\Commands\Esi\Update\Contracts;
use tehraven\Seat\CorporationOnlyCommands\Commands\Esi\Update\Killmails;
use tehraven\Seat\CorporationOnlyCommands\Commands\Esi\Update\PublicInfo;

/**
 * Class CorporationOnlyCommandsServiceProvider.
 *
 * @package tehraven\Seat\CorporationOnlyCommands
 */
class CorporationOnlyCommandsServiceProvider extends AbstractSeatPlugin
{
    public function boot()
    {
        $this->addCommands();
    }

    private function addCommands()
    {
        $this->commands([
            Contracts::class,
            Killmails::class,
            PublicInfo::class
        ]);
    }

    /**
     * Return the plugin public name as it should be displayed into settings.
     *
     * @return string
     * @example SeAT Web
     *
     */
    public function getName(): string
    {
        return 'Corporation-Only Commands';
    }

    /**
     * Return the plugin repository address.
     *
     * @example https://github.com/eveseat/web
     *
     * @return string
     */
    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/tehraven/seat-corporation-only-commands';
    }

    /**
     * Return the plugin technical name as published on package manager.
     *
     * @return string
     * @example web
     *
     */
    public function getPackagistPackageName(): string
    {
        return 'seat-corporation-only-commands';
    }

    /**
     * Return the plugin vendor tag as published on package manager.
     *
     * @return string
     * @example eveseat
     *
     */
    public function getPackagistVendorName(): string
    {
        return 'tehraven';
    }

    /**
     * Return the plugin installed version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return '1.0.0';
    }
}
