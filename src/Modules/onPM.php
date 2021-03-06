<?php

use OceanProject\Models\Log;

$onPM = function (int $who, string $message) {

    $bot = OceanProject\API\ActionAPI::getBot();

    if (!isset($bot->users[$who])) {
        return;
    }

    $regname = $bot->users[$who]->getRegname();

    $log = new Log;
    $log->chatid = $bot->data->chatid;
    $log->chatname = $bot->data->chatname;
    $log->typemessage = 2;
    $log->message = '[PM] ' . (!is_null($regname) ? $regname . ' (' . $who . ')' : $who) . ' sent: "' . $message . '"';
    $log->save();

    if (empty($message)) {
        return;
    }

    if (!empty($bot->snitchlist)) {
        foreach ($bot->snitchlist as $snitch) {
            if (isset($bot->users[$snitch['xatid']])) {
                $bot->network->sendPrivateConversation($snitch['xatid'], 'PM - [' . $who . '] ' . $message);
            }
        }
    }
};
