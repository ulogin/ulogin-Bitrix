<?php


class Ulogin
{

//    public static function genDisplayName($profile)
//    {
//        if (isset($profile['first_name']) && isset($profile['last_name'])) {
//            return $profile['first_name'] . ' ' . $profile['last_name'];
//        }
//        elseif (isset($profile['nickname']))
//        {
//            return $profile['nickname'];
//        }
//
//        $identity_component = parse_url($profile['identity']);
//
//        $result = $identity_component['host'];
//        if ($identity_component['path'] != '/') {
//            $result .= $identity_component['path'];
//        }
//
//        return $result . $identity_component['query'];
//
//    }


    public static function genNickname($profile)
    {
        if (isset($profile['nickname'])) {
            return $profile['nickname'];
        } elseif (isset($profile['email']) && preg_match('/^(.+)\@/i', $profile['email'], $nickname)) {
            return $nickname[1];
        } elseif (isset($profile['first_name']) && isset($profile['last_name'])) {
            return $this->normalize(iconv('utf-8', 'windows-1251', $profile['first_name'] . ' ' . $profile['last_name']), '_');
        }

        return 'user'.rand(1000,100000);
    }


}

?>