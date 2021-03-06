<?php

$active = function (int $who, array $message, int $type) {

    $bot  = OceanProject\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'active')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    $now  = time();
    $userTime = $now - OceanProject\API\DataAPI::get('active_' . $who);
    $displayName = $bot->users[$who]->isRegistered() ? $bot->users[$who]->getRegname() . '(' .
        $bot->users[$who]->getID() . ')'  : $bot->users[$who]->getID();

    $bot->network->sendMessageAutoDetection(
        $who,
        $bot->botlang('active.string', [$displayName, $bot->secondsToTime($userTime)]),
        $type
    );
};
