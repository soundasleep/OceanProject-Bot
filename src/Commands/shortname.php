<?php
$shortname = function (int $who, array $message, int $type) {

    $bot = OceanProject\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'shortname')) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            $bot->botlang('not.enough.rank'),
            $type
        );
    }

    if (!isset($message[1]) || empty($message[1])) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'Usage: !shortname [name]',
            $type,
            true
        );
    }

    if (!ctype_alnum($message[1])) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'Shortname contains bad characters.',
            $type
        );
    }

    if (strlen($message[1]) < 4) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'Too short for a shortname. Minimum 4 letters.',
            $type
        );
    }

    if (strlen($message[1]) > 9) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'Too long for a shortname. Maximum 9 letters.',
            $type
        );
    }

    if (is_numeric($message[1][0])) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'Shortnames can\'t start with a number.',
            $type
        );
    }
    
    $stream = [];
    $stream['http']['method'] = 'POST';
    $stream['http']['header'] = 'Content-Type: application/x-www-form-urlencoded';
    $stream['http']['content'] = 'GroupName=' . $message[1] . '&Quote=Get+cost&agree=ON';
    $stream['http']['timeout'] = 1;
    
    $res = file_get_contents('https://xat.com/web_gear/chat/BuyShortName.php', false, stream_context_create($stream));

    if (!$res) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'Cannot access page right now.',
            $type
        );
    }

    $res = explode('<ul data-localize=buy.rulesname>', $res)[1];

    if (strpos($res, 'Name is not allowed.') !== false) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'The shortname ' . $message[1] . ' is not allowed.',
            $type
        );
    }

    if (strpos($res, 'Sorry, name already taken') !== false) {
        if (strpos($res, '(1)')!== false) {
            return $bot->network->sendMessageAutoDetection(
                $who,
                'The shortname ' . $message[1] . ' is taken but can be released via ticket.',
                $type
            );
        } else {
            return $bot->network->sendMessageAutoDetection(
                $who,
                'The shortname ' . $message[1] . ' is taken and cannot be released via ticket.',
                $type
            );
        }
    }

    if (preg_match('<input type="hidden" name="Xats" value="(.*?)">', $res, $matches)) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            $message[1] . ' costs ' . number_format($matches[1]) .' xats.',
            $type
        );
    }

    $bot->network->sendMessageAutoDetection($who, 'Unknown error.', $type);
};
