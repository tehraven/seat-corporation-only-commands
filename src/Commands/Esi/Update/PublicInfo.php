<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015 to 2021 Leon Jacobs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace tehraven\Seat\CorporationOnlyCommands\Commands\Esi\Update;

use Illuminate\Console\Command;
use Seat\Eveapi\Bus\Corporation;
use Seat\Eveapi\Jobs\Universe\Names;
use Seat\Eveapi\Models\Alliances\Alliance;
use Seat\Eveapi\Models\Corporation\CorporationInfo;

/**
 * Class PublicInfo.
 *
 * @package tehraven\Seat\CorporationOnlyCommands\Commands\Esi\Update
 */
class PublicInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esi:update:corp-public';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule updater jobs for public information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Names::dispatch();

        CorporationInfo::all()->each(function ($corporation) {
            (new Corporation($corporation->corporation_id))->fire();
        });

        Alliance::has('corporations.characters.refresh_token')->each(function ($alliance) {
            $alliance->members->each(function ($member) {
                if (!$member->corporation()->exists()) {
                    (new Corporation($member->corporation_id))->fire();
                }
            });
        });
    }
}
