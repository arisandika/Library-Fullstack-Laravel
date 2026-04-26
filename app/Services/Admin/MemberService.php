<?php

namespace App\Services\Admin;

use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberService
{
    /**
     * @param array $data
     * @return Member
     */

    public function store(array $data): Member
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            return Member::create($data);
        });
    }

    /**
     * @param Member $member
     * @param array $data
     * @return Member
     */

    public function update(Member $member, array $data): Member
    {
        return DB::transaction(function () use ($member, $data) {
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $member->update($data);
            return $member;
        });
    }
}