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
use Seat\Eveapi\Jobs\Killmails\Corporation\Recent as RecentCorporationKills;
use Seat\Eveapi\Jobs\Killmails\Detail;
use Seat\Eveapi\Models\Killmails\Killmail;
use Seat\Eveapi\Models\RefreshToken;

/**
 * Class Killmails.
 *
 * @package tehraven\Seat\CorporationOnlyCommands\Commands\Esi\Update
 */
class Killmails extends Command
{
    /**
     * @var string
     */
    protected $signature = 'esi:update:corp-killmails {killmail_ids?* : Optional killmail_ids to update}';

    /**
     * @var string
     */
    protected $description = 'Schedule update jobs for corporation killmails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // collect optional kills ID from arguments
        $killmail_ids = $this->argument('killmail_ids') ?: [];

        $killmails = Killmail::whereDoesntHave('detail');

        // in case at least one ID has been provided, filter kills on arguments
        if (!empty($killmail_ids))
            $killmails->whereIn('killmail_id', $killmail_ids);

        // loop over kills and queue detailed jobs
        // if we don't have any kills registered -> queue character and corporation jobs to collect them
        if ($killmails->get()->each(function ($killmail) {
            Detail::dispatch($killmail->killmail_id, $killmail->killmail_hash);
        })->isEmpty() && empty($killmail_ids)) {
            RefreshToken::whereHas('character.affiliation', function ($query) {
                $query->whereNotNull('corporation_id');
            })->whereHas('character.corporation_roles', function ($query) {
                $query->where('scope', 'roles');
                $query->where('role', 'Director');
            })->get()->unique('character.affiliation.corporation_id')->each(function ($token) {
                RecentCorporationKills::dispatch($token->character->affiliation->corporation_id, $token);
            });
        }
    }
}
